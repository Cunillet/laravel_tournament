/**
 * Player chips showing match participants.
 *
 * @param {{
 *   players: Array<{ id: number, user?: { nickname: string } }>,
 *   currentPlayerId: number|null,
 * }} props
 */
export default function PlayerChips({ players, currentPlayerId }) {
    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">Jugadores</h2>
            <div className="players-list">
                {players?.map(player => (
                    <div
                        key={player.id}
                        className={`player-chip ${player.id === currentPlayerId ? 'player-chip--me' : ''}`}
                    >
                        <span className="player-chip__avatar">
                            {player.user?.nickname?.charAt(0).toUpperCase() || '?'}
                        </span>
                        <span className="player-chip__name">
                            {player.user?.nickname || 'Jugador'}
                        </span>
                        {player.id === currentPlayerId && (
                            <span className="player-chip__badge">Tú</span>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}
