<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Round extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'description',
        'order',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function scoringRules(): HasMany
    {
        return $this->hasMany(ScoringRule::class);
    }
}
