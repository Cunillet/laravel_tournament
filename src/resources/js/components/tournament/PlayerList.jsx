/** List of registered tournament players. */
export default function PlayerList({ players }) {
    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">Jugadores ({players?.length || 0})</h2>
            {(!players || players.length === 0) ? (
                <p className="game-detail__empty">No hay jugadores inscritos.</p>
            ) : (
                <div className="rounds-list">
                    {players.map(player => (
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
    );
}
