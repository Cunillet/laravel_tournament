<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_rounds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_match_id')->constrained('game_matches')->cascadeOnDelete();
            $table->foreignId('round_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['game_match_id', 'order']);
            $table->index(['game_match_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_rounds');
    }
};
