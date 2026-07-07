import { Link, Head } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Show({ auth, game }) {
    return (
        <>
            <Head title={game.name} />
            <Layout auth={auth}>
                <div className="game-detail">
                    <div className="game-detail__header">
                        <Link href={route('games.index')} className="btn btn--secondary">
                            ← Volver
                        </Link>
                        <Link href={route('games.edit', game.id)} className="btn btn--primary">
                            Editar juego
                        </Link>
                    </div>

                    <h1 className="game-detail__title">{game.name}</h1>

                    {game.description && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Descripción</h2>
                            <p className="game-detail__text">{game.description}</p>
                        </div>
                    )}

                    {game.objectives && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Objetivos</h2>
                            <p className="game-detail__text">{game.objectives}</p>
                        </div>
                    )}

                    <div className="game-detail__section">
                        <h2 className="game-detail__section-title">
                            Rondas ({game.rounds.length})
                        </h2>
                        {game.rounds.length === 0 ? (
                            <p className="game-detail__empty">No hay rondas definidas.</p>
                        ) : (
                            <div className="rounds-list">
                                {game.rounds.map(round => (
                                    <div key={round.id} className="round-card">
                                        <span className="round-card__order">#{round.order}</span>
                                        <div>
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
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="game-detail__section">
                        <h2 className="game-detail__section-title">
                            Normas de puntuación ({game.scoring_rules.length})
                        </h2>
                        {game.scoring_rules.length === 0 ? (
                            <p className="game-detail__empty">No hay normas de puntuación.</p>
                        ) : (
                            <div className="rules-list">
                                {game.scoring_rules.map(rule => (
                                    <div key={rule.id} className="rule-card">
                                        <div className="rule-card__header">
                                            <h3 className="rule-card__name">{rule.name}</h3>
                                            <span className="rule-card__system">
                                                {rule.scoring_system.name}
                                            </span>
                                        </div>
                                        {rule.description && (
                                            <p className="rule-card__desc">{rule.description}</p>
                                        )}
                                        <div className="rule-card__meta">
                                            {rule.priority !== null && (
                                                <span>Prioridad: {rule.priority}</span>
                                            )}
                                            {rule.min_score !== null && (
                                                <span>Mín: {rule.min_score}</span>
                                            )}
                                            {rule.max_score !== null && (
                                                <span>Máx: {rule.max_score}</span>
                                            )}
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
