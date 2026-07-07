<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\RoundDefinition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MatchRound extends Model
{
    protected $fillable = [
        'game_match_id',
        'round_id',
        'status',
        'order',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function gameMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'game_match_id');
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(RoundDefinition::class, 'round_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(MatchScore::class);
    }
}
