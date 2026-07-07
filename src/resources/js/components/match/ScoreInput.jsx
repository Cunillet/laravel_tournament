import { useState, useCallback, useEffect } from 'react';
import { router } from '@inertiajs/react';

/**
 * Score input that auto-saves on blur.
 *
 * @param {{
 *   matchRoundId: number,
 *   matchPlayerId: number,
 *   scoringRule: { id: number, name: string, min_score?: number, max_score?: number },
 *   initialScore: string|number,
 *   disabled: boolean,
 * }} props
 */
export default function ScoreInput({ matchRoundId, matchPlayerId, scoringRule, initialScore, disabled }) {
    const [value, setValue] = useState(initialScore ?? '');

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
