import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Show({ auth, tournament, standings }) {
    const user = auth.user;
    const isJoined = tournament.players?.some(p => p.user_id === user?.id);
    const canManage = user?.role === 0 || user?.role === 1;
    const canJoin = tournament.status === 'pending' && !isJoined;
    const canLeave = tournament.status === 'pending' && isJoined;
    const canStart = canManage && tournament.status === 'pending' && tournament.players_count >= 2;
    const canCreateRound = canManage && tournament.status === 'active';
    const hasActiveRound = tournament.rounds?.some(r => r.status === 'active');
    const canClose = canManage && tournament.status === 'active';

    const handleAction = (url, method = 'post') => {
        router[method](url);
    };

    return (
        <>
            <Head title={tournament.name} />
            <Layout auth={auth}>
                <div className="game-detail">
                    <div className="game-detail__header">
                        <a href={route('tournaments.index')} className="btn btn--secondary">← Volver</a>
                        <span className={`tournament-badge tournament-badge--${tournament.status}`}>
                            {tournament.status === 'pending' ? 'Pendiente' :
                             tournament.status === 'active' ? 'Activo' : 'Cerrado'}
                        </span>
                    </div>

                    <h1 className="game-detail__title">{tournament.name}</h1>

                    {tournament.description && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Descripción</h2>
                            <p className="game-detail__text">{tournament.description}</p>
                        </div>
                    )}

                    <div className="game-detail__section">
                        <h2 className="game-detail__section-title">Información</h2>
                        <p className="game-detail__text">
                            <strong>Juego:</strong> {tournament.game?.name}<br />
                            <strong>Creado por:</strong> {tournament.creator?.nickname}<br />
                            <strong>Jugadores:</strong> {tournament.players_count}
                        </p>
                    </div>

                    {/* Player actions */}
                    <div className="game-edit__section" style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
                        {canJoin && (
                            <button className="btn btn--primary" onClick={() => handleAction(route('tournaments.join', tournament.id))}>
                                Unirse al torneo
                            </button>
                        )}
                        {canLeave && (
                            <button className="btn btn--secondary" onClick={() => handleAction(route('tournaments.leave', tournament.id))}>
                                Salir del torneo
                            </button>
                        )}
                        {canStart && (
                            <button className="btn btn--primary" onClick={() => handleAction(route('tournaments.start', tournament.id))}>
                                Iniciar torneo
                            </button>
                        )}
                        {canCreateRound && (
                            <button className="btn btn--primary" onClick={() => handleAction(route('tournaments.rounds.store', tournament.id))}>
                                + Crear ronda
                            </button>
                        )}
                        {hasActiveRound && (
                            <button className="btn btn--secondary" onClick={() => {
                                const activeRound = tournament.rounds.find(r => r.status === 'active');
                                if (activeRound) handleAction(route('tournaments.rounds.close', activeRound.id));
                            }}>
                                Cerrar ronda activa
                            </button>
                        )}
                        {canClose && (
                            <button className="btn btn--secondary" style={{ color: '#ef4444' }} onClick={() => {
                                if (confirm('¿Cerrar el torneo definitivamente?')) handleAction(route('tournaments.close', tournament.id));
                            }}>
                                Cerrar torneo
                            </button>
                        )}
                    </div>

                    {/* Players list */}
                    <div className="game-detail__section">
                        <h2 className="game-detail__section-title">Jugadores ({tournament.players_count})</h2>
                        {tournament.players?.length === 0 ? (
                            <p className="game-detail__empty">No hay jugadores inscritos.</p>
                        ) : (
                            <div className="rounds-list">
                                {tournament.players?.map(player => (
                                    <div key={player.id} className="round-card">
                                        <span className="round-card__order">#{player.user_id}</span>
                                        <div className="round-card__content">
                                            <h3 className="round-card__name">{player.user?.nickname}</h3>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Standings / accumulated results */}
                    {tournament.rounds && tournament.rounds.length > 0 && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Clasificación general</h2>
                            {standings ? (
                                <div className="standings">
                                    <table className="standings__table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Jugador</th>
                                                {standings.rules.map(rule => (
                                                    <th key={rule.id}>{rule.name}</th>
                                                ))}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {standings.players.map((p, index) => (
                                                <tr key={p.user.id} className={index < 3 ? `standings__row--top${index + 1}` : ''}>
                                                    <td className="standings__pos">
                                                        {index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : index + 1}
                                                    </td>
                                                    <td className="standings__player">{p.user.nickname}</td>
                                                    {standings.rules.map(rule => (
                                                        <td key={rule.id} className="standings__value">
                                                            {p.scores[rule.id] ?? '—'}
                                                        </td>
                                                    ))}
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                <p className="game-detail__empty">
                                    No hay resultados disponibles. Las partidas deben estar finalizadas para mostrar la clasificación.
                                </p>
                            )}
                        </div>
                    )}

                    {/* Rounds */}
                    <div className="game-detail__section">
                        <h2 className="game-detail__section-title">
                            Rondas ({tournament.rounds?.length || 0})
                        </h2>
                        {(!tournament.rounds || tournament.rounds.length === 0) ? (
                            <p className="game-detail__empty">No hay rondas creadas.</p>
                        ) : (
                            <div className="rounds-list">
                                {tournament.rounds.map(round => (
                                    <div key={round.id} className="game-edit__section" style={{ border: '1px solid #334155', borderRadius: '8px', padding: '1rem', marginBottom: '1rem' }}>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
                                            <h3 style={{ margin: 0, fontSize: '1.1rem' }}>
                                                Ronda {round.round_number}
                                            </h3>
                                            <span className={`tournament-badge tournament-badge--${round.status}`}>
                                                {round.status === 'pending' ? 'Pendiente' :
                                                 round.status === 'active' ? 'Activa' : 'Cerrada'}
                                            </span>
                                        </div>

                                        {round.matches?.length > 0 && (
                                            <div className="rounds-list" style={{ gap: '0.5rem' }}>
                                                {round.matches.map(tm => (
                                                    <a
                                                        key={tm.id}
                                                        href={route('matches.show', tm.game_match_id)}
                                                        className="round-card"
                                                        style={{ padding: '0.75rem', textDecoration: 'none', cursor: 'pointer' }}
                                                    >
                                                        <div className="round-card__content">
                                                            <p className="round-card__name" style={{ fontSize: '0.95rem' }}>
                                                                {tm.game_match?.players?.map(p => p.user?.nickname).join(' vs ') || 'Partida'}
                                                            </p>
                                                            <span style={{ fontSize: '0.8rem', color: '#94a3b8' }}>
                                                                Estado: {tm.game_match?.status === 'pending' ? 'En juego' : 'Finalizada'}
                                                            </span>
                                                        </div>
                                                    </a>
                                                ))}
                                            </div>
                                        )}
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
