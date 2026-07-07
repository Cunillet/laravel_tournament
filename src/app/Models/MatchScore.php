<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MatchScore extends Model
{
    protected $fillable = [
        'match_round_id',
        'match_player_id',
        'scoring_rule_id',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function matchRound(): BelongsTo
    {
        return $this->belongsTo(MatchRound::class);
    }

    public function matchPlayer(): BelongsTo
    {
        return $this->belongsTo(MatchPlayer::class);
    }

    public function scoringRule(): BelongsTo
    {
        return $this->belongsTo(ScoringRule::class);
    }
}
