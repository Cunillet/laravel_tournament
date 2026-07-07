import { useState, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';
import MatchHeader from '../../components/match/MatchHeader';
import PlayerChips from '../../components/match/PlayerChips';
import ScoresSummaryTable from '../../components/match/ScoresSummaryTable';
import PhaseSection from '../../components/match/PhaseSection';
import RoundModal from '../../components/match/RoundModal';

export default function Show({ auth, match, currentPlayerId, currentPlayerFinished }) {
    const isPending = match.status === 'pending';
    const canEdit = isPending && currentPlayerId;
    const awaitingOther = isPending && currentPlayerFinished;

    const [modal, setModal] = useState(null);

    // Group rounds by round_definition
    const phases = useMemo(() => {
        const map = {};
        for (const mr of match.rounds || []) {
            const rdId = mr.round_id;
            if (!map[rdId]) {
                map[rdId] = {
                    roundDefinition: mr.round,
                    matchRounds: [],
                };
            }
            map[rdId].matchRounds.push(mr);
        }
        for (const key of Object.keys(map)) {
            map[key].matchRounds.sort((a, b) => a.order - b.order);
        }
        return Object.values(map);
    }, [match.rounds]);

    // Inject repetition index into each match round for the modal
    const phasesWithIndex = useMemo(() => {
        return phases.map(phase => ({
            ...phase,
            matchRounds: phase.matchRounds.map((mr, i) => ({
                ...mr,
                _repetitionIndex: i,
            })),
        }));
    }, [phases]);

    // Build a summary of total scores per scoring rule per player (for completed matches)
    const scoresSummary = useMemo(() => {
        if (match.status !== 'completed' || !match.rounds) return null;

        // Collect all unique scoring rules across all rounds
        const rulesMap = {};
        for (const mr of match.rounds) {
            for (const rule of (mr.round?.scoringRules || [])) {
                if (!rulesMap[rule.id]) {
                    rulesMap[rule.id] = { id: rule.id, name: rule.name };
                }
            }
        }

        // Sum scores per rule per player
        const playerTotals = {};
        for (const player of (match.players || [])) {
            playerTotals[player.id] = {};
            for (const ruleId of Object.keys(rulesMap)) {
                playerTotals[player.id][ruleId] = 0;
            }
        }

        for (const mr of match.rounds) {
            for (const sc of (mr.scores || [])) {
                if (playerTotals[sc.match_player_id]?.[sc.scoring_rule_id] !== undefined) {
                    playerTotals[sc.match_player_id][sc.scoring_rule_id] += parseFloat(sc.score) || 0;
                }
            }
        }

        return { rules: Object.values(rulesMap), players: match.players, playerTotals };
    }, [match.status, match.rounds, match.players]);

    const handleFinishScoring = () => {
        if (!confirm('¿Finalizar tu puntuación? Ya no podrás modificarla hasta que el otro jugador también finalice.')) return;
        router.post(route('matches.player-finish', match.id));
    };

    return (
        <>
            <Head title={match.game?.name || 'Partida'} />
            <Layout auth={auth}>
                <div className="match-page">
                    <MatchHeader match={match} />

                    <PlayerChips players={match.players} currentPlayerId={currentPlayerId} />

                    <ScoresSummaryTable scoresSummary={scoresSummary} />

                    {phasesWithIndex.map(phase => (
                        <PhaseSection
                            key={phase.roundDefinition.id}
                            phase={phase}
                            currentPlayerId={currentPlayerId}
                            onOpenRound={(mr, phaseName) => setModal({ matchRound: mr, phaseName })}
                        />
                    ))}

                    {isPending && !currentPlayerFinished && (
                        <div className="game-edit__section" style={{ marginTop: '1.5rem' }}>
                            <button className="btn btn--primary btn--lg" onClick={handleFinishScoring}>
                                Finalizar mi puntuación
                            </button>
                        </div>
                    )}

                    {awaitingOther && (
                        <div className="game-edit__section" style={{ marginTop: '1.5rem' }}>
                            <p className="match-waiting">
                                Has finalizado tu puntuación. Esperando al otro jugador…
                            </p>
                        </div>
                    )}
                </div>

                {modal && (
                    <RoundModal
                        matchRound={modal.matchRound}
                        phaseName={modal.phaseName}
                        currentPlayerId={canEdit ? currentPlayerId : null}
                        onClose={() => setModal(null)}
                    />
                )}
            </Layout>
        </>
    );
}
