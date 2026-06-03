<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    private const TABLE_NAME = 'champions';

    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('player_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('league_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->timestamps(6);
                $table->unique(['user_id', 'league_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
