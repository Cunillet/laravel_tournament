/**
 * List of round definitions for a game.
 *
 * @param {{ rounds: Array<{ id: number, order: number, name: string, description?: string, rounds_count?: number }> }} props
 */
export default function RoundDefList({ rounds }) {
    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">
                Rondas ({rounds.length})
            </h2>
            {rounds.length === 0 ? (
                <p className="game-detail__empty">No hay rondas definidas.</p>
            ) : (
                <div className="rounds-list">
                    {rounds.map(round => (
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
    );
}
