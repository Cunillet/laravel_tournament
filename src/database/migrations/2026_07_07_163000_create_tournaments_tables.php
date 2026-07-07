<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'game_id']);
        });

        Schema::create('tournament_players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tournament_id', 'user_id']);
        });

        Schema::create('tournament_rounds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('round_number');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['tournament_id', 'round_number']);
            $table->index(['tournament_id', 'status']);
        });

        Schema::create('tournament_matches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tournament_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_match_id')->constrained('game_matches')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tournament_round_id', 'game_match_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
        Schema::dropIfExists('tournament_rounds');
        Schema::dropIfExists('tournament_players');
        Schema::dropIfExists('tournaments');
    }
};
