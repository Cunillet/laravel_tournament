<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class RoundDefinition extends Model
{
    protected $table = 'round_definitions';

    protected $fillable = [
        'game_id',
        'name',
        'description',
        'order',
        'rounds_count',
    ];

    protected function casts(): array
    {
        return [
            'rounds_count' => 'integer',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function scoringRules(): HasMany
    {
        return $this->hasMany(ScoringRule::class, 'round_id');
    }
}
