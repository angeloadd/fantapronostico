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
                $table->foreignId('user_id')->unique()->constrained();
                $table->foreignId('team_id')->constrained();
                $table->foreignId('player_id')->constrained();
                $table->timestamps(6);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
