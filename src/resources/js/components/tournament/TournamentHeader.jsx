/** Tournament header with back button, status badge, and title. */
export default function TournamentHeader({ tournament }) {
    const statusLabel =
        tournament.status === 'pending' ? 'Pendiente' :
        tournament.status === 'active'  ? 'Activo'   : 'Cerrado';

    return (
        <>
            <div className="game-detail__header">
                <a href={route('tournaments.index')} className="btn btn--secondary">
                    ← Volver
                </a>
                <span className={`tournament-badge tournament-badge--${tournament.status}`}>
                    {statusLabel}
                </span>
            </div>
            <h1 className="game-detail__title">{tournament.name}</h1>
        </>
    );
}
