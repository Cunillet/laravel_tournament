import { useState } from 'react';
import { router } from '@inertiajs/react';

export default function RoundForm({ game, round, onClose }) {
    const [form, setForm] = useState({
        name: round?.name ?? '',
        description: round?.description ?? '',
        order: round?.order ?? game.rounds.length + 1,
        rounds_count: round?.rounds_count ?? 1,
    });

    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        setForm(prev => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);

        if (round) {
            router.put(route('games.round-definitions.update', { game: game.id, roundDefinition: round.id }), form, {
                onFinish: () => { setProcessing(false); onClose(); },
            });
        } else {
            router.post(route('games.round-definitions.store', game.id), form, {
                onFinish: () => { setProcessing(false); onClose(); },
            });
        }
    };

    return (
        <form onSubmit={handleSubmit} className="auth-card" style={{ maxWidth: '500px', marginBottom: '1rem' }}>
            <h3 style={{ marginBottom: '1rem' }}>
                {round ? 'Editar ronda' : 'Nueva ronda'}
            </h3>

            <div className="form-group">
                <label className="form-label" htmlFor="round-name">Nombre</label>
                <input
                    id="round-name" name="name" type="text"
                    className="form-input"
                    value={form.name} onChange={handleChange} required
                />
            </div>

            <div className="form-group">
                <label className="form-label" htmlFor="round-desc">Descripción</label>
                <textarea
                    id="round-desc" name="description" rows={2}
                    className="form-input form-input--textarea"
                    value={form.description} onChange={handleChange}
                />
            </div>

            <div className="form-group">
                <label className="form-label" htmlFor="round-order">Orden</label>
                <input
                    id="round-order" name="order" type="number" min="0"
                    className="form-input"
                    value={form.order} onChange={handleChange}
                    style={{ maxWidth: '100px' }}
                />
            </div>

            <div className="form-group">
                <label className="form-label" htmlFor="rounds-count">Repeticiones</label>
                <input
                    id="rounds-count" name="rounds_count" type="number" min="1"
                    className="form-input"
                    value={form.rounds_count} onChange={handleChange}
                    style={{ maxWidth: '100px' }}
                />
                <p style={{ fontSize: '0.8rem', color: '#94a3b8', marginTop: '0.25rem' }}>
                    ¿Cuántas veces se juega esta ronda?
                </p>
            </div>

            <div className="form__actions">
                <button type="button" className="btn btn--secondary" onClick={onClose}>
                    Cancelar
                </button>
                <button type="submit" className="btn btn--primary" disabled={processing}>
                    {processing ? 'Guardando...' : (round ? 'Actualizar' : 'Añadir')}
                </button>
            </div>
        </form>
    );
}
