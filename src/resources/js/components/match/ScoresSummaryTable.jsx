/**
 * Summary table of scores per rule per player for completed matches.
 *
 * @param {{
 *   scoresSummary: { rules: Array<{ id: number, name: string }>, players: Array<{ id: number, user?: { nickname: string } }>, playerTotals: Record<string, Record<string, number>> } | null
 * }} props
 */
export default function ScoresSummaryTable({ scoresSummary }) {
    if (!scoresSummary) return null;

    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">Resumen de puntuaciones</h2>
            <div className="scores-summary">
                <table className="scores-summary__table">
                    <thead>
                        <tr>
                            <th>Puntuación</th>
                            {scoresSummary.players.map(p => (
                                <th key={p.id}>{p.user?.nickname || 'Jugador'}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {scoresSummary.rules.map(rule => (
                            <tr key={rule.id}>
                                <td className="scores-summary__rule">{rule.name}</td>
                                {scoresSummary.players.map(p => (
                                    <td key={p.id} className="scores-summary__value">
                                        {scoresSummary.playerTotals[p.id]?.[rule.id] ?? '—'}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
