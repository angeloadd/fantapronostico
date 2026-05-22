<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Dto;

use App\Enums\GameStatus;

final readonly class GameDto
{
    public function __construct(
        public int $apiId,
        public int $homeTeamApiId,
        public int $awayTeamApiId,
        public int $startedAt,
        public string $stage,
        public GameStatus $status,
        public int $tournamentApiId
    ) {}

    public static function getGameType(string $round): string
    {
        if (str_contains(mb_strtolower($round), 'group')) {
            return 'group';
        }

        if ('Final' === $round) {
            return 'final';
        }

        return 'round';
    }

    /**
     * @return array{
     *     api_id: int,
     *     home_team_id: int,
     *     away_team_id: int,
     *     started_at: int,
     *     stage: string,
     *     status: GameStatus,
     *     tournament_id: int
     * }
     */
    public function toArray(): array
    {
        return [
            'api_id' => $this->apiId,
            'home_team_id' => $this->homeTeamApiId,
            'away_team_id' => $this->awayTeamApiId,
            'started_at' => $this->startedAt,
            'stage' => $this->stage,
            'status' => $this->status,
            'tournament_id' => $this->tournamentApiId,
        ];
    }
}
