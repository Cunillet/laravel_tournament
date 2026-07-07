import { useState, useCallback, useMemo, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

/*
 * Score input that auto-saves on blur.
 */
function ScoreInput({ matchRoundId, matchPlayerId, scoringRule, initialScore, disabled }) {
    const [value, setValue] = useState(initialScore ?? '');

    // Sync when initialScore changes (e.g. on successful save)
    useEffect(() => {
        setValue(initialScore ?? '');
    }, [initialScore]);

    const handleBlur = useCallback(() => {
        const num = parseFloat(value);
        if (isNaN(num)) return;
        if (String(initialScore) === String(num)) return;

        router.post(
            route('matches.rounds.scores.upsert', matchRoundId),
            {
                match_player_id: matchPlayerId,
                scoring_rule_id: scoringRule.id,
                score: num,
            },
            { preserveScroll: true }
        );
    }, [value, matchRoundId, matchPlayerId, scoringRule.id, initialScore]);

    return (
        <input
            type="number"
            step="1"
            className="modal-score__input"
            value={value}
            onChange={e => setValue(e.target.value)}
            onBlur={handleBlur}
            disabled={disabled}
            min={scoringRule.min_score ?? undefined}
            max={scoringRule.max_score ?? undefined}
            placeholder="—"
        />
    );
}

/*
 * Modal showing all scoring rules for a given match round.
 */
function RoundModal({ matchRound, phaseName, matchPlayers, currentPlayerId, canEdit, onClose }) {
    const idx = matchRound._repetitionIndex;

    // Close on backdrop click
    const handleBackdrop = useCallback((e) => {
        if (e.target === e.currentTarget) onClose();
    }, [onClose]);

    // Close on Escape
    useEffect(() => {
        const handler = (e) => { if (e.key === 'Escape') onClose(); };
        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [onClose]);

    const getPlayerScore = (ruleId) => {
        if (!currentPlayerId) return '';
        const s = matchRound.scores?.find(
            sc => sc.match_player_id === currentPlayerId && sc.scoring_rule_id === ruleId
        );
        return s?.score ?? '';
    };

    const rules = matchRound.round?.scoringRules || [];

    return (
        <div className="modal-backdrop" onClick={handleBackdrop}>
            <div className="modal">
                <div className="modal__header">
                    <h2 className="modal__title">
                        {phaseName}
                        <span className="modal__subtitle">Ronda {idx + 1}</span>
                    </h2>
                    <button className="modal__close" onClick={onClose}>&times;</button>
                </div>

                <div className="modal__body">
                    {rules.length === 0 ? (
                        <p className="modal__empty">Esta ronda no tiene tipos de puntuación.</p>
                    ) : (
                        <div className="modal-score-list">
                            {rules.map(rule => (
                                <div key={rule.id} className="modal-score-row">
                                    <div className="modal-score-row__info">
                                        <span className="modal-score-row__name">
                                            {rule.name}
                                            {rule.description && (
                                                <span
                                                    className="modal-score-row__tooltip"
                                                    title={rule.description}
                                                >
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                        <circle cx="8" cy="8" r="6.5" stroke="currentColor" strokeWidth="1.2"/>
                                                        <path d="M6.5 6.5C6.5 5.67 7.17 5 8 5s1.5.67 1.5 1.5c0 1-1.5 1.5-1.5 1.5" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round"/>
                                                        <circle cx="8" cy="10.5" r="0.5" fill="currentColor"/>
                                                    </svg>
                                                </span>
                                            )}
                                        </span>
                                        {(rule.min_score !== null || rule.max_score !== null) && (
                                            <span className="modal-score-row__range">
                                                {rule.min_score ?? '—'} - {rule.max_score ?? '—'}
                                            </span>
                                        )}
                                    </div>
                                    <div className="modal-score-row__input">
                                        <ScoreInput
                                            matchRoundId={matchRound.id}
                                            matchPlayerId={currentPlayerId}
                                            scoringRule={rule}
                                            initialScore={getPlayerScore(rule.id)}
                                            disabled={!canEdit}
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {!canEdit && (
                    <div className="modal__footer">
                        <span className="modal__locked">Partida finalizada — solo lectura</span>
                    </div>
                )}
            </div>
        </div>
    );
}

export default function Show({ auth, match, currentPlayerId, currentPlayerFinished }) {
    const isPending = match.status === 'pending';
    const user = auth.user;
    const canEdit = isPending && currentPlayerId;
    const awaitingOther = isPending && currentPlayerFinished;

    // Opened modal state: { matchRound, phaseName } or null
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

    const hasAnyScore = (matchRound) => {
        if (!currentPlayerId) return false;
        return matchRound.scores?.some(s => s.match_player_id === currentPlayerId) ?? false;
    };

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

    const handleCloseMatch = () => {
        if (!confirm('¿Finalizar la partida? Ya no podrás modificar las puntuaciones.')) return;
        router.post(route('matches.close', match.id));
    };

    return (
        <>
            <Head title={match.game?.name || 'Partida'} />
            <Layout auth={auth}>
                <div className="match-page">
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

                    {/* Player chips */}
                    <div className="game-detail__section">
                        <h2 className="game-detail__section-title">Jugadores</h2>
                        <div className="players-list">
                            {match.players?.map(player => (
                                <div
                                    key={player.id}
                                    className={`player-chip ${player.id === currentPlayerId ? 'player-chip--me' : ''}`}
                                >
                                    <span className="player-chip__avatar">
                                        {player.user?.nickname?.charAt(0).toUpperCase() || '?'}
                                    </span>
                                    <span className="player-chip__name">
                                        {player.user?.nickname || 'Jugador'}
                                    </span>
                                    {player.id === currentPlayerId && (
                                        <span className="player-chip__badge">Tú</span>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Scores summary for completed matches */}
                    {scoresSummary && (
                        <div className="game-detail__section">
                            <h2 className="game-detail__section-title">Resumen de puntuaciones</h2>
                            <div className="scores-summary">
                                <table className="scores-summary__table">
                                    <thead>
                                        <tr>
                                            <th>Puntuaci&oacute;n</th>
                                            {scoresSummary.players.map(p => (
                                                <th key={p.id}>{p.user?.nickname || 'Jugador'}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {scoresSummary.rules.map(rule => (
                                            <tr key={rule.id}>
                                                <td className="scores-summary__rule">{rule.name}</td>
                                                {scoresSummary.players.map(p => (
                                                    <td key={p.id} className="scores-summary__value">
                                                        {scoresSummary.playerTotals[p.id]?.[rule.id] ?? '—'}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {/* Phases with clickable round cards */}
                    {phasesWithIndex.map(phase => (
                        <div key={phase.roundDefinition.id} className="game-detail__section">
                            <h2 className="game-detail__section-title">
                                {phase.roundDefinition.name}
                            </h2>
                            {phase.roundDefinition.description && (
                                <p className="game-detail__text">{phase.roundDefinition.description}</p>
                            )}

                            <div className="rounds-list">
                                {phase.matchRounds.map((mr) => (
                                    <button
                                        key={mr.id}
                                        className="round-btn"
                                        onClick={() => setModal({ matchRound: mr, phaseName: phase.roundDefinition.name })}
                                    >
                                        <div className="round-btn__left">
                                            <span className="round-btn__order">Ronda {mr._repetitionIndex + 1}</span>
                                            {phase.matchRounds.length > 1 && (
                                                <span className="round-btn__reps">
                                                    {mr._repetitionIndex + 1} de {phase.matchRounds.length}
                                                </span>
                                            )}
                                        </div>
                                        <div className="round-btn__right">
                                            {hasAnyScore(mr) ? (
                                                <span className="round-btn__done">Puntuada</span>
                                            ) : (
                                                <span className="round-btn__pending">Pendiente</span>
                                            )}
                                            <span className="round-btn__arrow">&rsaquo;</span>
                                        </div>
                                    </button>
                                ))}
                            </div>
                        </div>
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

                {/* Modal */}
                {modal && (
                    <RoundModal
                        matchRound={modal.matchRound}
                        phaseName={modal.phaseName}
                        matchPlayers={match.players}
                        currentPlayerId={currentPlayerId}
                        canEdit={canEdit}
                        onClose={() => setModal(null)}
                    />
                )}
            </Layout>
        </>
    );
}
