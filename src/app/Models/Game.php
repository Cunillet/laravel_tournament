<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\RoundDefinition;

final class Game extends Model
{
    protected $fillable = [
        'name',
        'description',
        'objectives',
    ];

    public function rounds(): HasMany
    {
        return $this->hasMany(RoundDefinition::class)->orderBy('order');
    }

    public function scoringRules(): HasMany
    {
        return $this->hasMany(ScoringRule::class);
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    public function matchRounds(): HasManyThrough
    {
        return $this->hasManyThrough(MatchRound::class, GameMatch::class);
    }
}
