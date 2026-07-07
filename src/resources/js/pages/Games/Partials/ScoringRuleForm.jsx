import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';

export default function ScoringRuleForm({ game, rule, onClose }) {
    const { scoringSystems } = usePage().props;

    const [form, setForm] = useState({
        name: rule?.name ?? '',
        description: rule?.description ?? '',
        scoring_system_id: rule?.scoring_system_id ?? (scoringSystems?.[0]?.id ?? ''),
        round_id: rule?.round_id ?? '',
        min_score: rule?.min_score ?? '',
        max_score: rule?.max_score ?? '',
        priority: rule?.priority ?? '',
    });

    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        const value = e.target.type === 'number' && e.target.value !== ''
            ? Number(e.target.value)
            : e.target.value;

        setForm(prev => ({ ...prev, [e.target.name]: value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);

        const payload = {
            ...form,
            round_id: form.round_id || null,
            min_score: form.min_score || null,
            max_score: form.max_score || null,
            priority: form.priority || null,
        };

        if (rule) {
            router.put(
                route('games.scoring-rules.update', { game: game.id, scoringRule: rule.id }),
                payload,
                { onFinish: () => { setProcessing(false); onClose(); } }
            );
        } else {
            router.post(
                route('games.scoring-rules.store', game.id),
                payload,
                { onFinish: () => { setProcessing(false); onClose(); } }
            );
        }
    };

    return (
        <form onSubmit={handleSubmit} className="auth-card" style={{ maxWidth: '500px', marginBottom: '1rem' }}>
            <h3 style={{ marginBottom: '1rem' }}>
                {rule ? 'Editar norma de puntuación' : 'Nueva norma de puntuación'}
            </h3>

            <div className="form-group">
                <label className="form-label" htmlFor="rule-name">Nombre</label>
                <input
                    id="rule-name" name="name" type="text"
                    className="form-input"
                    value={form.name} onChange={handleChange} required
                />
            </div>

            <div className="form-group">
                <label className="form-label" htmlFor="rule-desc">Descripción</label>
                <textarea
                    id="rule-desc" name="description" rows={2}
                    className="form-input form-input--textarea"
                    value={form.description} onChange={handleChange}
                />
            </div>

            <div className="form-group">
                <label className="form-label" htmlFor="rule-system">Sistema de puntuación</label>
                <select
                    id="rule-system" name="scoring_system_id"
                    className="form-input form-input--select"
                    value={form.scoring_system_id}
                    onChange={handleChange}
                    required
                >
                    <option value="">Selecciona un sistema...</option>
                    {scoringSystems?.map(system => (
                        <option key={system.id} value={system.id}>
                            {system.name}
                        </option>
                    ))}
                </select>
            </div>

            <div className="form-group">
                <label className="form-label" htmlFor="rule-round">
                    Ronda (opcional — si se deja vacío aplica a todas)
                </label>
                <select
                    id="rule-round" name="round_id"
                    className="form-input form-input--select"
                    value={form.round_id}
                    onChange={handleChange}
                >
                    <option value="">Todas las rondas</option>
                    {game.rounds?.map(round => (
                        <option key={round.id} value={round.id}>
                            #{round.order} - {round.name}
                        </option>
                    ))}
                </select>
            </div>

            <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap' }}>
                <div className="form-group" style={{ flex: 1, minWidth: '120px' }}>
                    <label className="form-label" htmlFor="rule-min">Puntuación mínima</label>
                    <input
                        id="rule-min" name="min_score" type="number" step="0.01"
                        className="form-input"
                        value={form.min_score} onChange={handleChange}
                        placeholder="0"
                    />
                </div>

                <div className="form-group" style={{ flex: 1, minWidth: '120px' }}>
                    <label className="form-label" htmlFor="rule-max">Puntuación máxima</label>
                    <input
                        id="rule-max" name="max_score" type="number" step="0.01"
                        className="form-input"
                        value={form.max_score} onChange={handleChange}
                        placeholder="100"
                    />
                </div>

                <div className="form-group" style={{ flex: 1, minWidth: '100px' }}>
                    <label className="form-label" htmlFor="rule-priority">Prioridad</label>
                    <input
                        id="rule-priority" name="priority" type="number" min="0"
                        className="form-input"
                        value={form.priority} onChange={handleChange}
                        placeholder="1"
                    />
                </div>
            </div>

            <div className="form__actions">
                <button type="button" className="btn btn--secondary" onClick={onClose}>
                    Cancelar
                </button>
                <button type="submit" className="btn btn--primary" disabled={processing}>
                    {processing ? 'Guardando...' : (rule ? 'Actualizar' : 'Añadir')}
                </button>
            </div>
        </form>
    );
}
