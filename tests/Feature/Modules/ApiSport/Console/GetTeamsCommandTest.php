<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\ApiSport\Console;

use App\Models\Tournament;
use Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class GetTeamsCommandTest extends TestCase
{
    use GetMockResponseTrait;

    private const FIRST_DTO = [
        'api_id' => 1,
        'is_national' => true,
        'code' => 'BEL',
        'name' => 'Belgium',
        'logo' => 'https://media.api-sports.io/football/teams/1.png',
    ];

    private const SECOND_DTO = [
        'api_id' => 2,
        'is_national' => true,
        'code' => 'FRA',
        'name' => 'France',
        'logo' => 'https://media.api-sports.io/football/teams/2.png',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        if (!Config::has('api-sport.host')) {
            $this->fail('ApiSport host not configured');
        }
        Config::set('api-sport.host', 'api-sport-host');
        Http::fake([
            'api-sport-host/*' => Http::response($this->getResponse('teams.json')),
        ]);

        Tournament::factory()->euro()->create();
    }

    public function test_handle(): void
    {
        /**
         * @var PendingCommand $pendingCommand
         */
        $pendingCommand = $this->artisan('fp:teams:get');
        $pendingCommand
            ->expectsOutput('console: Successfully updated 24 teams')
            ->assertOk()
            ->run();

        $this->assertDatabaseHas('teams', self::FIRST_DTO);
        $this->assertDatabaseHas('teams', self::SECOND_DTO);
    }
}
