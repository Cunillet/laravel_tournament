import { useEffect, useCallback } from 'react';
import ScoreInput from './ScoreInput';

/**
 * Modal showing all scoring rules for a given match round.
 *
 * @param {{
 *   matchRound: { id: number, _repetitionIndex: number, round?: { scoringRules: Array }, scores?: Array },
 *   phaseName: string,
 *   currentPlayerId: number|null,
 *   onClose: () => void,
 * }} props
 */
export default function RoundModal({ matchRound, phaseName, currentPlayerId, onClose }) {
    const idx = matchRound._repetitionIndex;

    const handleBackdrop = useCallback((e) => {
        if (e.target === e.currentTarget) onClose();
    }, [onClose]);

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
                                                <span className="modal-score-row__tooltip" title={rule.description}>
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
                                            disabled={!currentPlayerId}
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {!currentPlayerId && (
                    <div className="modal__footer">
                        <span className="modal__locked">Partida finalizada — solo lectura</span>
                    </div>
                )}
            </div>
        </div>
    );
}
