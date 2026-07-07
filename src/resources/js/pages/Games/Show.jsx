import { Head } from '@inertiajs/react';
import Layout from '../../components/Layout';
import GameHeader from '../../components/game/GameHeader';
import RoundDefList from '../../components/game/RoundDefList';
import ScoringRuleList from '../../components/game/ScoringRuleList';

export default function Show({ auth, game }) {
    return (
        <>
            <Head title={game.name} />
            <Layout auth={auth}>
                <div className="game-detail">
                    <GameHeader game={game} />

                    {game.description && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Descripción</h2>
                            <p className="game-detail__text">{game.description}</p>
                        </div>
                    )}

                    {game.objectives && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Objetivos</h2>
                            <p className="game-detail__text">{game.objectives}</p>
                        </div>
                    )}

                    <RoundDefList rounds={game.rounds} />
                    <ScoringRuleList rules={game.scoring_rules} />
                </div>
            </Layout>
        </>
    );
}
