<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_players', function (Blueprint $table): void {
            $table->timestamp('finished_at')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('match_players', function (Blueprint $table): void {
            $table->dropColumn('finished_at');
        });
    }
};
