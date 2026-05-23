<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\ApiSport\Console;

use App\Models\Tournament;
use App\Modules\Tournament\Models\Team;
use Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class GetGamesCommandTest extends TestCase
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
            'api-sport-host/*' => Http::response($this->getResponse('fixtures.json')),
        ]);

        Tournament::factory(['api_id' => 4])->euro()->create();

        Team::factory(4)->sequence(
            ['api_id' => 25],
            ['api_id' => 1108],
            ['api_id' => 769],
            ['api_id' => 15],
        )->create();
    }

    public function test_handle(): void
    {
        /**
         * @var PendingCommand $pendingCommand
         */
        $pendingCommand = $this->artisan('fp:games:get');
        $pendingCommand
            ->expectsOutput('console: Successfully updated 2 games')
            ->assertOk()
            ->run();

        $this->assertDatabaseHas('games', [
            'id' => 1145509,
            'tournament_id' => 1,
            'started_at' => '2024-06-14 19:00:00',
            'status' => 'finished',
            'stage' => 'group',
        ]);
        $this->assertDatabaseHas('games', [
            'id' => 1145510,
            'tournament_id' => 1,
            'started_at' => '2024-06-15 13:00:00',
            'status' => 'finished',
            'stage' => 'group',
        ]);
    }
}
