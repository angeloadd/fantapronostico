<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public const TABLE_NAME = 'predictions';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->foreignId('game_id')->constrained();
                $table->integer('home_score');
                $table->integer('away_score');
                $table->enum('sign', ['1', 'x', '2']);
                $table->integer('home_scorer_id')->nullable();
                $table->integer('away_scorer_id')->nullable();
                $table->foreignId('league_id')
                    ->default(1)
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
                $table->timestamps(6);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
