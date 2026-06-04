<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Service;

use App\Modules\ApiSport\Dto\GameGoalsDto;
use App\Modules\ApiSport\Dto\GamesDto;
use App\Modules\ApiSport\Dto\GameStatusDto;
use App\Modules\ApiSport\Dto\NationalsDto;
use App\Modules\ApiSport\Dto\PlayersDto;
use App\Modules\ApiSport\Dto\TeamsDto;
use App\Modules\ApiSport\Dto\WinnerDto;
use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use App\Modules\ApiSport\Request\GetGameEventsRequest;
use App\Modules\ApiSport\Request\GetGamesRequest;
use App\Modules\ApiSport\Request\GetGameStatusRequest;
use App\Modules\ApiSport\Request\GetPlayersByNationalRequest;
use App\Modules\ApiSport\Request\GetTeamsRequest;
use App\Modules\ApiSport\Request\GetTopScorersRequest;
use App\Modules\ApiSport\Request\GetWinnerRequest;
use ErrorException;
use Illuminate\Http\Client\ConnectionException;

interface ApiSportServiceInterface
{
    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getTeamsBySeasonAndLeague(GetTeamsRequest $request): TeamsDto;

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getGamesBySeasonAndLeague(GetGamesRequest $request): GamesDto;

    /**
     * @param  GetPlayersByNationalRequest[]  $requests
     *
     * @throws ConnectionException
     * @throws InvalidApisportTokenException
     * @throws ErrorException
     */
    public function getPlayersByNational(array $requests, int $rateInSeconds = 0): NationalsDto;

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getGameStatus(GetGameStatusRequest $request): GameStatusDto;

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getGameGoals(GetGameEventsRequest $request): GameGoalsDto;

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getTopScorers(GetTopScorersRequest $request): PlayersDto;

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getWinner(GetWinnerRequest $request): WinnerDto;
}
