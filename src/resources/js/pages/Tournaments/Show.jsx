import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';
import TournamentHeader from '../../components/tournament/TournamentHeader';
import TournamentInfo from '../../components/tournament/TournamentInfo';
import TournamentActions from '../../components/tournament/TournamentActions';
import PlayerList from '../../components/tournament/PlayerList';
import StandingsTable from '../../components/tournament/StandingsTable';
import RoundList from '../../components/tournament/RoundList';

export default function Show({ auth, tournament, standings }) {
    const user = auth.user;
    const isJoined = tournament.players?.some(p => p.user_id === user?.id);
    const canManage = user?.role === 0 || user?.role === 1;
    const canJoin = tournament.status === 'pending' && !isJoined;
    const canLeave = tournament.status === 'pending' && isJoined;
    const canStart = canManage && tournament.status === 'pending' && tournament.players_count >= 2;
    const canCreateRound = canManage && tournament.status === 'active';
    const hasActiveRound = tournament.rounds?.some(r => r.status === 'active');
    const canClose = canManage && tournament.status === 'active';

    const handleAction = (url, method = 'post') => {
        router[method](url);
    };

    const handleCloseRound = () => {
        const activeRound = tournament.rounds.find(r => r.status === 'active');
        if (activeRound) handleAction(route('tournaments.rounds.close', activeRound.id));
    };

    const handleClose = () => {
        if (confirm('¿Cerrar el torneo definitivamente?')) handleAction(route('tournaments.close', tournament.id));
    };

    const handleDelete = () => {
        if (confirm('¿Eliminar el torneo y todas sus partidas? Esta acción no se puede deshacer.')) {
            handleAction(route('tournaments.destroy', tournament.id), 'delete');
        }
    };

    const canDelete = user?.role === 0;

    return (
        <>
            <Head title={tournament.name} />
            <Layout auth={auth}>
                <div className="game-detail">
                    <TournamentHeader tournament={tournament} />

                    {tournament.description && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Descripción</h2>
                            <p className="game-detail__text">{tournament.description}</p>
                        </div>
                    )}

                    <TournamentInfo tournament={tournament} />

                    <TournamentActions
                        canJoin={canJoin}
                        canLeave={canLeave}
                        canStart={canStart}
                        canCreateRound={canCreateRound}
                        hasActiveRound={hasActiveRound}
                        canClose={canClose}
                        canDelete={canDelete}
                        onJoin={() => handleAction(route('tournaments.join', tournament.id))}
                        onLeave={() => handleAction(route('tournaments.leave', tournament.id))}
                        onStart={() => handleAction(route('tournaments.start', tournament.id))}
                        onCreateRound={() => handleAction(route('tournaments.rounds.store', tournament.id))}
                        onCloseRound={handleCloseRound}
                        onClose={handleClose}
                        onDelete={handleDelete}
                    />

                    <PlayerList players={tournament.players} />

                    {tournament.rounds && tournament.rounds.length > 0 && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Clasificación general</h2>
                            <StandingsTable standings={standings} />
                        </div>
                    )}

                    <RoundList rounds={tournament.rounds} />
                </div>
            </Layout>
        </>
    );
}
