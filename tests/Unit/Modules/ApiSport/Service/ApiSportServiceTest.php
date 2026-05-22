<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\ApiSport\Service;

use App\Modules\ApiSport\Client\ApiSportClientInterface;
use App\Modules\ApiSport\Dto\GamesDto;
use App\Modules\ApiSport\Dto\NationalDto;
use App\Modules\ApiSport\Dto\TeamsDto;
use App\Modules\ApiSport\Mapper\MapperInterface;
use App\Modules\ApiSport\Request\GetGamesRequest;
use App\Modules\ApiSport\Request\GetPlayersByNationalRequest;
use App\Modules\ApiSport\Request\GetTeamsRequest;
use App\Modules\ApiSport\Service\ApiSportService;
use Illuminate\Support\Sleep;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\UnitTestCase;

final class ApiSportServiceTest extends UnitTestCase
{
    private ApiSportClientInterface&MockObject $client;

    private MapperInterface&MockObject $mapper;

    private ApiSportService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(ApiSportClientInterface::class);
        $this->mapper = $this->createMock(MapperInterface::class);
        $this->subject = new ApiSportService($this->client, $this->mapper);
    }

    public function test_get_teams_by_season_and_league_returns_a_dto(): void
    {
        $response = ['parameters' => ['league' => 1], 'response' => ['teams' => ['ok']]];
        $this->client->expects($this->once())->method('get')
            ->with('teams', ['league' => 1, 'season' => 2050])
            ->willReturn($response);
        $this->mapper->expects($this->once())->method('map')
            ->with($response)
            ->willReturn(new TeamsDto($response['parameters']['league']));
        $this->subject->getTeamsBySeasonAndLeague(new GetTeamsRequest(1, 2050));
    }

    public function test_get_games_by_season_and_league_returns_a_dto(): void
    {
        $response = ['parameters' => ['league' => 1], 'response' => ['fixtures' => ['ok']]];
        $this->client->expects($this->once())->method('get')
            ->with('fixtures', ['league' => 1, 'season' => 2050])
            ->willReturn($response);
        $this->mapper->expects($this->once())->method('map')
            ->with($response)
            ->willReturn(new GamesDto());
        $this->subject->getGamesBySeasonAndLeague(new GetGamesRequest(1, 2050));
    }

    public function test_get_players_by_national_returns_a_dto(): void
    {
        Sleep::fake();
        $response = ['parameters' => ['national' => 1], 'response' => ['players' => ['ok']]];
        $this->client->expects($this->once())->method('get')
            ->with('/players/squads', ['team' => 1])
            ->willReturn($response);
        $this->mapper->expects($this->once())->method('map')
            ->with($response)
            ->willReturn(new NationalDto($response['parameters']['national']));
        $nationalsDto = $this->subject->getPlayersByNational([new GetPlayersByNationalRequest(1)], 2);
        $this->assertCount(1, $nationalsDto->nationals());
        Sleep::assertSleptTimes(1);
    }
}
