<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign keys referencing rounds table
        Schema::table('scoring_rules', function (Blueprint $table): void {
            $table->dropForeign(['round_id']);
        });

        Schema::table('match_rounds', function (Blueprint $table): void {
            $table->dropForeign(['round_id']);
        });

        // Rename table and add rounds_count
        Schema::rename('rounds', 'round_definitions');

        Schema::table('round_definitions', function (Blueprint $table): void {
            $table->unsignedSmallInteger('rounds_count')->default(1)->after('order');
        });

        // Re-add foreign keys pointing to the renamed table
        Schema::table('scoring_rules', function (Blueprint $table): void {
            $table->foreign('round_id')
                ->references('id')
                ->on('round_definitions')
                ->cascadeOnDelete();
        });

        Schema::table('match_rounds', function (Blueprint $table): void {
            $table->foreign('round_id')
                ->references('id')
                ->on('round_definitions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scoring_rules', function (Blueprint $table): void {
            $table->dropForeign(['round_id']);
        });

        Schema::table('match_rounds', function (Blueprint $table): void {
            $table->dropForeign(['round_id']);
        });

        Schema::table('round_definitions', function (Blueprint $table): void {
            $table->dropColumn('rounds_count');
        });

        Schema::rename('round_definitions', 'rounds');

        Schema::table('scoring_rules', function (Blueprint $table): void {
            $table->foreign('round_id')
                ->references('id')
                ->on('rounds')
                ->cascadeOnDelete();
        });

        Schema::table('match_rounds', function (Blueprint $table): void {
            $table->foreign('round_id')
                ->references('id')
                ->on('rounds')
                ->cascadeOnDelete();
        });
    }
};
