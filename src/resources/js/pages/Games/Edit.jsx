import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';
import RoundForm from './Partials/RoundForm';
import ScoringRuleForm from './Partials/ScoringRuleForm';

export default function Edit({ auth, game, errors }) {
    const [form, setForm] = useState({
        name: game.name,
        description: game.description ?? '',
        objectives: game.objectives ?? '',
    });

    const [processing, setProcessing] = useState(false);
    const [editingRound, setEditingRound] = useState(null);
    const [editingRule, setEditingRule] = useState(null);
    const [showRoundForm, setShowRoundForm] = useState(false);
    const [showRuleForm, setShowRuleForm] = useState(false);

    const handleChange = (e) => {
        setForm(prev => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleUpdateGame = (e) => {
        e.preventDefault();
        setProcessing(true);
        router.put(route('games.update', game.id), form, {
            onFinish: () => setProcessing(false),
        });
    };

    const handleDeleteGame = () => {
        if (confirm('¿Eliminar este juego? Se perderán todos los datos asociados.')) {
            router.delete(route('games.destroy', game.id));
        }
    };

    const handleDeleteRound = (round) => {
        if (confirm(`¿Eliminar la ronda "${round.name}"?`)) {
            router.delete(route('games.round-definitions.destroy', { game: game.id, roundDefinition: round.id }));
        }
    };

    const handleDeleteRule = (rule) => {
        if (confirm(`¿Eliminar la norma "${rule.name}"?`)) {
            router.delete(
                route('games.scoring-rules.destroy', { game: game.id, scoringRule: rule.id })
            );
        }
    };

    return (
        <>
            <Head title={`Editar: ${game.name}`} />
            <Layout auth={auth}>
                <div className="game-edit">
                    <div className="game-edit__header">
                        <a href={route('games.show', game.id)} className="btn btn--secondary">
                            ← Ver juego
                        </a>
                        <button className="btn btn--secondary" onClick={handleDeleteGame} style={{ color: '#ef4444' }}>
                            Eliminar juego
                        </button>
                    </div>

                    <h1 className="game-edit__title">Editar: {game.name}</h1>

                    {/* Game form */}
                    <form onSubmit={handleUpdateGame} className="auth-card" style={{ maxWidth: '600px', marginBottom: '2rem' }}>
                        <div className="form-group">
                            <label className="form-label" htmlFor="name">Nombre</label>
                            <input
                                id="name" name="name" type="text"
                                className={`form-input${errors?.name ? ' form-input--error' : ''}`}
                                value={form.name} onChange={handleChange} required
                            />
                            {errors?.name && <p className="form-error">{errors.name}</p>}
                        </div>

                        <div className="form-group">
                            <label className="form-label" htmlFor="description">Descripción</label>
                            <textarea
                                id="description" name="description" rows={3}
                                className={`form-input form-input--textarea${errors?.description ? ' form-input--error' : ''}`}
                                value={form.description} onChange={handleChange}
                            />
                            {errors?.description && <p className="form-error">{errors.description}</p>}
                        </div>

                        <div className="form-group">
                            <label className="form-label" htmlFor="objectives">Objetivos</label>
                            <textarea
                                id="objectives" name="objectives" rows={3}
                                className={`form-input form-input--textarea${errors?.objectives ? ' form-input--error' : ''}`}
                                value={form.objectives} onChange={handleChange}
                            />
                            {errors?.objectives && <p className="form-error">{errors.objectives}</p>}
                        </div>

                        <button type="submit" className="btn btn--primary" disabled={processing}>
                            {processing ? 'Guardando...' : 'Guardar cambios'}
                        </button>
                    </form>

                    {/* Rounds section */}
                    <div className="game-edit__section">
                        <div className="game-edit__section-header">
                            <h2>Rondas ({game.rounds.length})</h2>
                            <button
                                className="btn btn--primary"
                                onClick={() => { setEditingRound(null); setShowRoundForm(true); }}
                            >
                                + Añadir ronda
                            </button>
                        </div>

                        {showRoundForm && (
                            <RoundForm
                                game={game}
                                round={editingRound}
                                onClose={() => { setShowRoundForm(false); setEditingRound(null); }}
                            />
                        )}

                        {game.rounds.length === 0 ? (
                            <p className="game-detail__empty">No hay rondas definidas.</p>
                        ) : (
                            <div className="rounds-list">
                                {game.rounds.map(round => (
                                    <div key={round.id} className="round-card">
                                        <span className="round-card__order">#{round.order}</span>
                                        <div className="round-card__content">
                                            <h3 className="round-card__name">{round.name}</h3>
                                            {round.description && (
                                                <p className="round-card__desc">{round.description}</p>
                                            )}
                                            {round.rounds_count > 1 && (
                                                <p className="round-card__repetitions">
                                                    Se repite {round.rounds_count} veces
                                                </p>
                                            )}
                                        </div>
                                        <div className="round-card__actions">
                                            <button
                                                className="btn btn--secondary"
                                                onClick={() => { setEditingRound(round); setShowRoundForm(true); }}
                                            >
                                                Editar
                                            </button>
                                            <button
                                                className="btn btn--secondary"
                                                onClick={() => handleDeleteRound(round)}
                                                style={{ color: '#ef4444' }}
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Scoring rules section */}
                    <div className="game-edit__section">
                        <div className="game-edit__section-header">
                            <h2>Normas de puntuación ({game.scoring_rules.length})</h2>
                            <button
                                className="btn btn--primary"
                                onClick={() => { setEditingRule(null); setShowRuleForm(true); }}
                            >
                                + Añadir norma
                            </button>
                        </div>

                        {showRuleForm && (
                            <ScoringRuleForm
                                game={game}
                                rule={editingRule}
                                onClose={() => { setShowRuleForm(false); setEditingRule(null); }}
                            />
                        )}

                        {game.scoring_rules.length === 0 ? (
                            <p className="game-detail__empty">No hay normas de puntuación.</p>
                        ) : (
                            <div className="rules-list">
                                {game.scoring_rules.map(rule => (
                                    <div key={rule.id} className="rule-card">
                                        <div className="rule-card__header">
                                            <h3 className="rule-card__name">{rule.name}</h3>
                                            <span className="rule-card__system">{rule.scoring_system.name}</span>
                                        </div>
                                        {rule.description && (
                                            <p className="rule-card__desc">{rule.description}</p>
                                        )}
                                        <div className="rule-card__meta">
                                            {rule.priority !== null && <span>Prioridad: {rule.priority}</span>}
                                            {rule.min_score !== null && <span>Mín: {rule.min_score}</span>}
                                            {rule.max_score !== null && <span>Máx: {rule.max_score}</span>}
                                        </div>
                                        <div className="rule-card__actions">
                                            <button
                                                className="btn btn--secondary"
                                                onClick={() => { setEditingRule(rule); setShowRuleForm(true); }}
                                            >
                                                Editar
                                            </button>
                                            <button
                                                className="btn btn--secondary"
                                                onClick={() => handleDeleteRule(rule)}
                                                style={{ color: '#ef4444' }}
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </Layout>
        </>
    );
}
