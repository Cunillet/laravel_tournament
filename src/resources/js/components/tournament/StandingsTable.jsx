/** Tournament standings table with per-rule scores and podium medals. */
export default function StandingsTable({ standings }) {
    if (!standings) {
        return (
            <p className="game-detail__empty">
                No hay resultados disponibles. Las partidas deben estar finalizadas para mostrar la clasificación.
            </p>
        );
    }

    return (
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
    );
}
