/**
 * List of scoring rules for a game.
 *
 * @param {{ rules: Array<{ id: number, name: string, description?: string, priority?: number|null, min_score?: number|null, max_score?: number|null, scoring_system: { name: string } }> }} props
 */
export default function ScoringRuleList({ rules }) {
    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">
                Normas de puntuación ({rules.length})
            </h2>
            {rules.length === 0 ? (
                <p className="game-detail__empty">No hay normas de puntuación.</p>
            ) : (
                <div className="rules-list">
                    {rules.map(rule => (
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
    );
}
