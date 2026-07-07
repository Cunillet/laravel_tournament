import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Create({ auth, errors }) {
    const [form, setForm] = useState({
        name: '',
        description: '',
        objectives: '',
    });

    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        setForm(prev => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        router.post(route('games.store'), form, {
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title="Crear Juego" />
            <Layout auth={auth}>
                <div className="form-page">
                    <h1 className="form-page__title">Crear nuevo juego</h1>

                    <form onSubmit={handleSubmit} className="auth-card" style={{ maxWidth: '600px' }}>
                        <div className="form-group">
                            <label className="form-label" htmlFor="name">Nombre del juego</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                className={`form-input${errors?.name ? ' form-input--error' : ''}`}
                                value={form.name}
                                onChange={handleChange}
                                placeholder="Ej: Torneo de Poker"
                                required
                            />
                            {errors?.name && <p className="form-error">{errors.name}</p>}
                        </div>

                        <div className="form-group">
                            <label className="form-label" htmlFor="description">Descripción</label>
                            <textarea
                                id="description"
                                name="description"
                                className={`form-input form-input--textarea${errors?.description ? ' form-input--error' : ''}`}
                                value={form.description}
                                onChange={handleChange}
                                placeholder="Describe brevemente el juego..."
                                rows={4}
                            />
                            {errors?.description && <p className="form-error">{errors.description}</p>}
                        </div>

                        <div className="form-group">
                            <label className="form-label" htmlFor="objectives">Objetivos</label>
                            <textarea
                                id="objectives"
                                name="objectives"
                                className={`form-input form-input--textarea${errors?.objectives ? ' form-input--error' : ''}`}
                                value={form.objectives}
                                onChange={handleChange}
                                placeholder="¿Cuál es el objetivo del juego?"
                                rows={4}
                            />
                            {errors?.objectives && <p className="form-error">{errors.objectives}</p>}
                        </div>

                        <div className="form__actions">
                            <a href={route('games.index')} className="btn btn--secondary">Cancelar</a>
                            <button type="submit" className="btn btn--primary" disabled={processing}>
                                {processing ? 'Guardando...' : 'Crear juego'}
                            </button>
                        </div>
                    </form>
                </div>
            </Layout>
        </>
    );
}
