<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_match_id')->constrained('game_matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['game_match_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_players');
    }
};
