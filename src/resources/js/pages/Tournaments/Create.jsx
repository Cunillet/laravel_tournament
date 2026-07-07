import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Create({ auth, games, errors }) {
    const [form, setForm] = useState({
        name: '',
        description: '',
        game_id: '',
    });

    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        setForm(prev => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        router.post(route('tournaments.store'), form, {
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title="Crear Torneo" />
            <Layout auth={auth}>
                <div className="form-page">
                    <h1 className="form-page__title">Crear nuevo torneo</h1>

                    <form onSubmit={handleSubmit} className="auth-card" style={{ maxWidth: '600px' }}>
                        <div className="form-group">
                            <label className="form-label" htmlFor="name">Nombre del torneo</label>
                            <input
                                id="name" name="name" type="text"
                                className={`form-input${errors?.name ? ' form-input--error' : ''}`}
                                value={form.name} onChange={handleChange}
                                placeholder="Ej: Torneo de Poker Primavera"
                                required
                            />
                            {errors?.name && <p className="form-error">{errors.name}</p>}
                        </div>

                        <div className="form-group">
                            <label className="form-label" htmlFor="description">Descripción</label>
                            <textarea
                                id="description" name="description"
                                className={`form-input form-input--textarea${errors?.description ? ' form-input--error' : ''}`}
                                value={form.description} onChange={handleChange}
                                placeholder="Describe el torneo..."
                                rows={4}
                            />
                            {errors?.description && <p className="form-error">{errors.description}</p>}
                        </div>

                        <div className="form-group">
                            <label className="form-label" htmlFor="game_id">Juego</label>
                            <select
                                id="game_id" name="game_id"
                                className={`form-input form-input--select${errors?.game_id ? ' form-input--error' : ''}`}
                                value={form.game_id} onChange={handleChange}
                                required
                            >
                                <option value="">Selecciona un juego...</option>
                                {games.map(game => (
                                    <option key={game.id} value={game.id}>{game.name}</option>
                                ))}
                            </select>
                            {errors?.game_id && <p className="form-error">{errors.game_id}</p>}
                        </div>

                        <div className="form__actions">
                            <a href={route('tournaments.index')} className="btn btn--secondary">Cancelar</a>
                            <button type="submit" className="btn btn--primary" disabled={processing}>
                                {processing ? 'Creando...' : 'Crear torneo'}
                            </button>
                        </div>
                    </form>
                </div>
            </Layout>
        </>
    );
}
