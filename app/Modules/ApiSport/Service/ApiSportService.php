<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Service;

use App\Modules\ApiSport\Client\ApiSportClientInterface;
use App\Modules\ApiSport\Dto\GameGoalsDto;
use App\Modules\ApiSport\Dto\GamesDto;
use App\Modules\ApiSport\Dto\GameStatusDto;
use App\Modules\ApiSport\Dto\NationalDto;
use App\Modules\ApiSport\Dto\NationalsDto;
use App\Modules\ApiSport\Dto\PlayersDto;
use App\Modules\ApiSport\Dto\WinnerDto;
use App\Modules\ApiSport\Dto\TeamsDto;
use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use App\Modules\ApiSport\Exceptions\InvalidMappingException;
use App\Modules\ApiSport\Mapper\MapperInterface;
use App\Modules\ApiSport\Request\GetGameEventsRequest;
use App\Modules\ApiSport\Request\GetGamesRequest;
use App\Modules\ApiSport\Request\GetGameStatusRequest;
use App\Modules\ApiSport\Request\GetPlayersByNationalRequest;
use App\Modules\ApiSport\Request\GetTeamsRequest;
use App\Modules\ApiSport\Request\GetTopScorersRequest;
use App\Modules\ApiSport\Request\GetWinnerRequest;
use ErrorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Sleep;

final readonly class ApiSportService implements ApiSportServiceInterface
{
    public function __construct(
        private ApiSportClientInterface $apiSportClient,
        private MapperInterface $mapper,
    ) {}

    /**
     * @throws InvalidApisportTokenException
     * @throws ConnectionException
     * @throws ErrorException
     */
    public function getTeamsBySeasonAndLeague(GetTeamsRequest $request): TeamsDto
    {
        $response = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

        $gamesDto = $this->mapper->map($response);

        if (!$gamesDto instanceof TeamsDto) {
            throw InvalidMappingException::create($gamesDto::class, TeamsDto::class);
        }

        return $gamesDto;
    }

    /**
     * @throws ErrorException
     * @throws ConnectionException
     * @throws InvalidApisportTokenException
     */
    public function getGamesBySeasonAndLeague(GetGamesRequest $request): GamesDto
    {
        $response = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

        $gamesDto = $this->mapper->map($response);
        if (!$gamesDto instanceof GamesDto) {
            throw InvalidMappingException::create($gamesDto::class, GamesDto::class);
        }

        return $gamesDto;
    }

    /**
     * @param  GetPlayersByNationalRequest[]  $requests
     *
     * @throws ConnectionException
     * @throws InvalidApisportTokenException
     * @throws ErrorException
     */
    public function getPlayersByNational(array $requests, int $rateInSeconds = 0): NationalsDto
    {
        $nationals = new NationalsDto();
        foreach ($requests as $request) {
            $response = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

            $national = $this->mapper->map($response);

            if (!$national instanceof NationalDto) {
                throw InvalidMappingException::create($national::class, NationalDto::class);
            }

            $nationals->add($national);

            if ($rateInSeconds > 0) {
                Sleep::for($rateInSeconds)->seconds();
            }
        }

        return $nationals;
    }

    public function getGameStatus(GetGameStatusRequest $request): GameStatusDto
    {
        $externalResponse = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

        $mapping = $this->mapper->map($externalResponse);

        if (!$mapping instanceof GameStatusDto) {
            throw InvalidMappingException::create($mapping::class, GameStatusDto::class);
        }

        return $mapping;
    }

    /**
     * {@inheritDoc}
     */
    public function getGameGoals(GetGameEventsRequest $request): GameGoalsDto
    {
        $externalResponse = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

        $mapping = $this->mapper->map($externalResponse);

        if (!$mapping instanceof GameGoalsDto) {
            throw InvalidMappingException::create($mapping::class, GameGoalsDto::class);
        }

        return $mapping;
    }

    /**
     * {@inheritDoc}
     */
    public function getTopScorers(GetTopScorersRequest $request): PlayersDto
    {
        $externalResponse = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

        $mapping = $this->mapper->map($externalResponse);

        if (!$mapping instanceof PlayersDto) {
            throw InvalidMappingException::create($mapping::class, PlayersDto::class);
        }

        return $mapping;
    }

    /**
     * {@inheritDoc}
     */
    public function getWinner(GetWinnerRequest $request): WinnerDto
    {
        $externalResponse = $this->apiSportClient->get($request::ENDPOINT, $request->toQuery());

        $mapping = $this->mapper->map($externalResponse);

        if (!$mapping instanceof WinnerDto) {
            throw InvalidMappingException::create($mapping::class, WinnerDto::class);
        }

        return $mapping;
    }
}
