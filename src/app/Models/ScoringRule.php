<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ScoringRule extends Model
{
    protected $fillable = [
        'game_id',
        'round_id',
        'scoring_system_id',
        'name',
        'description',
        'min_score',
        'max_score',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    public function scoringSystem(): BelongsTo
    {
        return $this->belongsTo(ScoringSystem::class);
    }
}
