/**
 * Match page header with back button, status badge, and title.
 *
 * @param {{ match: { status: string, game?: { name: string, description?: string } } }} props
 */
export default function MatchHeader({ match }) {
    return (
        <>
            <div className="game-detail__header">
                <a href={route('matches.index')} className="btn btn--secondary">← Volver</a>
                <span className={`tournament-badge tournament-badge--${match.status}`}>
                    {match.status === 'pending' ? 'En juego' : 'Finalizada'}
                </span>
            </div>

            <h1 className="game-detail__title">
                {match.game?.name || 'Partida'}
            </h1>

            {match.game?.description && (
                <p className="game-detail__text">{match.game.description}</p>
            )}
        </>
    );
}
