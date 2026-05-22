<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\ApiSport\Console;

use App\Models\Tournament;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

final class GetPlayersByTeamCommandTest extends TestCase
{
    use GetMockResponseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        if ( ! Config::has('api-sport.host')) {
            $this->fail('ApiSport host not configured');
        }
        Config::set('api-sport.host', 'api-sport-host');
        Http::fake([
            '*?team=25' => Http::response($this->getResponse('players25.json')),
            '*?team=10' => Http::response($this->getResponse('players10.json')),
            '*?team=1' => Http::response($this->getResponse('players1.json')),
        ]);

        $tournament = Tournament::factory(['api_id' => 4])->euro()->create();
        League::create(
            [
                'tournament_id' => $tournament->id,
                'name' => 'Serie A',
            ]
        );

        $teams = Team::factory(3)->national()->sequence(
            ['api_id' => 1],
            ['api_id' => 25],
            ['api_id' => 10],
        )->create();

        $tournament->teams()->attach($teams->pluck('id'));
    }

    public function test_handle(): void
    {
        Sleep::fake();
        $this->artisan('fp:players:get')
            ->expectsOutput('console: successfully updated 84 players')
            ->assertOk()
            ->run();

        $this->assertDatabaseCount('players', 84);
        Sleep::assertSleptTimes(3);
        Http::assertSentCount(3);
    }
}
