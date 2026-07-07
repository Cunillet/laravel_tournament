import { Link, Head } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Index({ auth, tournaments }) {
    const canManage = auth.user?.role === 0 || auth.user?.role === 1;

    return (
        <>
            <Head title="Torneos" />
            <Layout auth={auth}>
                <div className="games-page">
                    <div className="games-page__header">
                        <h1 className="games-page__title">Torneos</h1>
                        {canManage && (
                            <Link href={route('tournaments.create')} className="btn btn--primary">
                                + Nuevo torneo
                            </Link>
                        )}
                    </div>

                    {tournaments.length === 0 ? (
                        <div className="empty-state">
                            <p className="empty-state__text">No hay torneos disponibles.</p>
                            {canManage && (
                                <Link href={route('tournaments.create')} className="btn btn--primary">
                                    Crear primer torneo
                                </Link>
                            )}
                        </div>
                    ) : (
                        <div className="games-list">
                            {tournaments.map(tournament => (
                                <div key={tournament.id} className="game-card">
                                    <div className="game-card__body">
                                        <h2 className="game-card__title">{tournament.name}</h2>
                                        <p className="game-card__description">
                                            {tournament.description || 'Sin descripción'}
                                        </p>
                                        <div className="game-card__meta">
                                            <span className={`tournament-badge tournament-badge--${tournament.status}`}>
                                                {tournament.status === 'pending' ? 'Pendiente' :
                                                 tournament.status === 'active' ? 'Activo' : 'Cerrado'}
                                            </span>
                                            <span>{tournament.players_count} jugadores</span>
                                            <span>{tournament.game?.name}</span>
                                            <span>Creado por: {tournament.creator?.nickname}</span>
                                        </div>
                                    </div>
                                    <div className="game-card__actions">
                                        <Link
                                            href={route('tournaments.show', tournament.id)}
                                            className="btn btn--secondary"
                                        >
                                            Ver
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
