<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class GameMatch extends Model
{
    protected $table = 'game_matches';

    protected $fillable = [
        'game_id',
        'status',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(MatchPlayer::class, 'game_match_id');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(MatchRound::class, 'game_match_id')->orderBy('order');
    }

    public function tournamentMatch(): HasOne
    {
        return $this->hasOne(TournamentMatch::class, 'game_match_id');
    }
}
