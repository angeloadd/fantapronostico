<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    private const TABLE_NAME = 'prediction_rank';

    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('prediction_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('league_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('game_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->boolean('is_exact');
                $table->boolean('is_sign');
                $table->boolean('is_home_scorer');
                $table->boolean('is_away_scorer');
                $table->smallInteger('value_exact');
                $table->smallInteger('value_sign');
                $table->smallInteger('value_scorer');
                $table->integer('total');
                $table->boolean('is_final');
                $table->timestamp('predicted_at', 6);
                $table->timestamps();
                $table->unique(['user_id', 'prediction_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
