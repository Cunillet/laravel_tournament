<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scoring_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('round_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('scoring_system_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('min_score', 10, 2)->nullable();
            $table->decimal('max_score', 10, 2)->nullable();
            $table->unsignedSmallInteger('priority')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['game_id', 'round_id']);
            $table->index(['game_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
    }
};
