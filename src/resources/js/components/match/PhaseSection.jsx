/**
 * A phase (round definition) section with clickable round cards.
 *
 * @param {{
 *   phase: { roundDefinition: { id: number, name: string, description?: string }, matchRounds: Array<{ id: number, _repetitionIndex: number, scores?: Array<{ match_player_id: number }> }> },
 *   currentPlayerId: number|null,
 *   onOpenRound: (matchRound: object, phaseName: string) => void,
 * }} props
 */
export default function PhaseSection({ phase, currentPlayerId, onOpenRound }) {
    const hasAnyScore = (matchRound) => {
        if (!currentPlayerId) return false;
        return matchRound.scores?.some(s => s.match_player_id === currentPlayerId) ?? false;
    };

    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">
                {phase.roundDefinition.name}
            </h2>
            {phase.roundDefinition.description && (
                <p className="game-detail__text">{phase.roundDefinition.description}</p>
            )}

            <div className="rounds-list">
                {phase.matchRounds.map((mr) => (
                    <button
                        key={mr.id}
                        className="round-btn"
                        onClick={() => onOpenRound(mr, phase.roundDefinition.name)}
                    >
                        <div className="round-btn__left">
                            <span className="round-btn__order">Ronda {mr._repetitionIndex + 1}</span>
                            {phase.matchRounds.length > 1 && (
                                <span className="round-btn__reps">
                                    {mr._repetitionIndex + 1} de {phase.matchRounds.length}
                                </span>
                            )}
                        </div>
                        <div className="round-btn__right">
                            {hasAnyScore(mr) ? (
                                <span className="round-btn__done">Puntuada</span>
                            ) : (
                                <span className="round-btn__pending">Pendiente</span>
                            )}
                            <span className="round-btn__arrow">&rsaquo;</span>
                        </div>
                    </button>
                ))}
            </div>
        </div>
    );
}
