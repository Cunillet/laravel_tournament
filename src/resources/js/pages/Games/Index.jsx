import { Link, Head } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Index({ auth, games }) {
    return (
        <>
            <Head title="Mis Juegos" />
            <Layout auth={auth}>
                <div className="games-page">
                    <div className="games-page__header">
                        <h1 className="games-page__title">Mis Juegos</h1>
                        <Link href={route('games.create')} className="btn btn--primary">
                            + Nuevo juego
                        </Link>
                    </div>

                    {games.length === 0 ? (
                        <div className="empty-state">
                            <p className="empty-state__text">Aún no hay juegos creados.</p>
                            <Link href={route('games.create')} className="btn btn--primary">
                                Crear primer juego
                            </Link>
                        </div>
                    ) : (
                        <div className="games-list">
                            {games.map(game => (
                                <div key={game.id} className="game-card">
                                    <div className="game-card__body">
                                        <h2 className="game-card__title">{game.name}</h2>
                                        <p className="game-card__description">
                                            {game.description || 'Sin descripción'}
                                        </p>
                                        <div className="game-card__meta">
                                            <span>{game.rounds_count} rondas</span>
                                            <span>{game.scoring_rules_count} normas</span>
                                        </div>
                                    </div>
                                    <div className="game-card__actions">
                                        <Link
                                            href={route('games.show', game.id)}
                                            className="btn btn--secondary"
                                        >
                                            Ver
                                        </Link>
                                        <Link
                                            href={route('games.edit', game.id)}
                                            className="btn btn--primary"
                                        >
                                            Editar
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </Layout>
        </>
    );
}
