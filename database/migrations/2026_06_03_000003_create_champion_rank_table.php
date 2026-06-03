<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    private const TABLE_NAME = 'champion_rank';

    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('league_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->boolean('winner');
                $table->boolean('top_scorer');
                $table->smallInteger('value_winner');
                $table->smallInteger('value_top_scorer');
                $table->integer('total');
                $table->timestamps();
                $table->unique(['user_id', 'league_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};