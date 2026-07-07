/**
 * Game detail header with back button, edit button, and title.
 *
 * @param {{ game: { id: number, name: string } }} props
 */
export default function GameHeader({ game }) {
    return (
        <>
            <div className="game-detail__header">
                <a href={route('games.index')} className="btn btn--secondary">
                    ← Volver
                </a>
                <a href={route('games.edit', game.id)} className="btn btn--primary">
                    Editar juego
                </a>
            </div>
            <h1 className="game-detail__title">{game.name}</h1>
        </>
    );
}
