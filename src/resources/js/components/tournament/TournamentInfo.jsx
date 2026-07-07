/** Tournament metadata: game, creator, player count. */
export default function TournamentInfo({ tournament }) {
    return (
        <div className="game-detail__section">
            <h2 className="game-detail__section-title">Información</h2>
            <p className="game-detail__text">
                <strong>Juego:</strong> {tournament.game?.name}<br />
                <strong>Creado por:</strong> {tournament.creator?.nickname}<br />
                <strong>Jugadores:</strong> {tournament.players_count}
            </p>
        </div>
    );
}
