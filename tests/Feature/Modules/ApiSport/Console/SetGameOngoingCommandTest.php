<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\ApiSport\Console;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\Tournament;
use App\Modules\League\Models\League;
use Tests\TestCase;

final class SetGameOngoingCommandTest extends TestCase
{
    public function test_handle_sets_not_started_games_ongoing_if_started_at_is_in_the_past(): void
    {
        $tournament = Tournament::factory()->create();
        League::create([
            'name' => 'test',
            'tournament_id' => $tournament->id,
        ]);
        $game1 = Game::factory()->create([
            'started_at' => now()->subMinute(),
            'tournament_id' => $tournament->id,
            'status' => GameStatus::NOT_STARTED,
        ]);

        $game2 = Game::factory()->create([
            'started_at' => now()->addDay(),
            'tournament_id' => $tournament->id,
            'status' => GameStatus::NOT_STARTED,
        ]);
        $this->artisan('fp:games:set-ongoing')
            ->assertOk()
            ->run();

        $this->assertDatabaseHas(
            'games',
            [
                'id' => $game1->id,
                'status' => 'ongoing',
            ]
        );
        $this->assertDatabaseHas(
            'games',
            [
                'id' => $game2->id,
                'status' => 'not_started',
            ]
        );
    }
}
