<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_player_id')->constrained('match_player')->cascadeOnDelete();
            $table->foreignId('scoring_rule_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['match_round_id', 'match_player_id']);
            $table->unique(['match_round_id', 'match_player_id', 'scoring_rule_id'], 'match_scores_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_scores');
    }
};
