<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\ApiSport\Console;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Modules\League\Models\League;
use Config;
use Http;
use Tests\TestCase;

final class GetGameGoalsCommandTest extends TestCase
{
    use GetMockResponseTrait;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Config::has('api-sport.host')) {
            $this->fail('ApiSport host not configured');
        }
        Config::set('api-sport.host', 'api-sport-host');
        Http::fake([
            'fixtures/events*' => Http::response($this->getResponse('gameGoals.json')),
            'fixtures?id*' => Http::response($this->getResponse('gameStatus.json')),
        ]);
    }

    public function test_handle_gets_game_goals_and_set_game_as_finished(): void
    {
        $tournament = Tournament::factory()->create();
        League::create([
            'name' => 'Test',
            'tournament_id' => $tournament->id,
        ]);
        Game::factory()->create([
            'id' => 1145509,
            'tournament_id' => $tournament->id,
            'status' => GameStatus::ONGOING,
        ]);
        Player::factory()->create(['id' => 203224]);
        Player::factory()->create(['id' => 181812]);
        Player::factory()->create(['id' => 978]);
        Player::factory()->create(['id' => 25391]);
        Player::factory()->create(['id' => 2285]);
        Player::factory()->create(['id' => 864]);
        $this->artisan('fp:games:goals:get')
            ->assertOk()
            ->run();

        $this->assertDatabaseHas(
            'game_goals',
            [
                'player_id' => 181812,
                'game_id' => 1145509,
                'is_autogoal' => false,
                'scored_at' => 19,
            ]
        );
        $this->assertDatabaseHas(
            'game_goals',
            [
                'player_id' => 978,
                'game_id' => 1145509,
                'is_autogoal' => false,
                'scored_at' => 46,
            ]
        );
        $this->assertDatabaseHas(
            'game_goals',
            [
                'player_id' => 203224,
                'game_id' => 1145509,
                'is_autogoal' => false,
                'scored_at' => 10,
            ]
        );
        $this->assertDatabaseHas(
            'game_goals',
            [
                'player_id' => 25391,
                'game_id' => 1145509,
                'is_autogoal' => false,
                'scored_at' => 68,
            ]
        );
        $this->assertDatabaseHas(
            'game_goals',
            [
                'player_id' => 2285,
                'game_id' => 1145509,
                'is_autogoal' => true,
                'scored_at' => 87,
            ]
        );
        $this->assertDatabaseHas(
            'game_goals',
            [
                'player_id' => 864,
                'game_id' => 1145509,
                'is_autogoal' => false,
                'scored_at' => 93,
            ]
        );
        $this->assertDatabaseCount('game_goals', 6);
    }
}
