/** List of tournament rounds with links to each match. */
export default function RoundList({ rounds }) {
    const statusLabel = (status) =>
        status === 'pending' ? 'Pendiente' :
        status === 'active'  ? 'Activa'    : 'Cerrada';

    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">
                Rondas ({rounds?.length || 0})
            </h2>
            {(!rounds || rounds.length === 0) ? (
                <p className="game-detail__empty">No hay rondas creadas.</p>
            ) : (
                <div className="rounds-list">
                    {rounds.map(round => (
                        <div key={round.id} className="game-edit__section" style={{
                            border: '1px solid #334155',
                            borderRadius: '8px',
                            padding: '1rem',
                            marginBottom: '1rem',
                        }}>
                            <div style={{
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                marginBottom: '0.75rem',
                            }}>
                                <h3 style={{ margin: 0, fontSize: '1.1rem' }}>
                                    Ronda {round.round_number}
                                </h3>
                                <span className={`tournament-badge tournament-badge--${round.status}`}>
                                    {statusLabel(round.status)}
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
    );
}
