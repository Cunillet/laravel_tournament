<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Tournament extends Model
{
    protected $fillable = [
        'name',
        'description',
        'game_id',
        'status',
        'created_by',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function players(): HasMany
    {
        return $this->hasMany(TournamentPlayer::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(TournamentRound::class)->orderBy('round_number');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class)->through('rounds');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Delete the tournament and all related data via DB cascade.
     *
     * FK cascadeOnDelete handles: tournament_players, tournament_rounds,
     * tournament_matches, and game_matches automatically.
     */
    public function purge(): void
    {
        $this->delete();
    }
}
