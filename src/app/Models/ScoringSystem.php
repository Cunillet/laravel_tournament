<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ScoringSystem extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function scoringRules(): HasMany
    {
        return $this->hasMany(ScoringRule::class);
    }
}
