import { Link, Head } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Index({ auth, matches }) {
    return (
        <>
            <Head title="Mis partidas" />
            <Layout auth={auth}>
                <div className="games-page">
                    <div className="games-page__header">
                        <h1 className="games-page__title">Mis partidas</h1>
                    </div>

                    {matches.length === 0 ? (
                        <div className="empty-state">
                            <p className="empty-state__text">No tienes partidas.</p>
                        </div>
                    ) : (
                        <div className="games-list">
                            {matches.map(match => (
                                <div key={match.id} className="game-card">
                                    <div className="game-card__body">
                                        <h2 className="game-card__title">
                                            {match.game?.name || 'Partida #' + match.id}
                                        </h2>
                                        <div className="game-card__meta">
                                            <span className={`tournament-badge tournament-badge--${match.status}`}>
                                                {match.status === 'pending' ? 'En juego' : 'Finalizada'}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="game-card__actions">
                                        <Link
                                            href={route('matches.show', match.id)}
                                            className="btn btn--secondary"
                                        >
                                            {match.status === 'completed' ? 'Ver' : 'Jugar'}
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
