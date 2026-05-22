<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use DateTimeInterface;

final class UserRank
{
    public function __construct(
        public readonly int $userId,
        public readonly string $userName,
        public readonly int $leagueId,
        public int $total = 0,
        public int $numberOfResults = 0,
        public int $numberOfSigns = 0,
        public int $numberOfScorers = 0,
        public int $finalBetTimestamp = 0,
        public int $finalBetTotal = 0,
        public bool $winner = false,
        public bool $topScorer = false,
        public ?DateTimeInterface $latestGameStartedAt = null
    ) {}

    public function finalPredictionTimestamp(): int
    {
        return $this->finalBetTimestamp;
    }

    public function finalPredictionTotal(): int
    {
        return $this->finalBetTotal;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function results(): int
    {
        return $this->numberOfResults;
    }

    public function signs(): int
    {
        return $this->numberOfSigns;
    }

    public function scorers(): int
    {
        return $this->numberOfScorers;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function winner(): bool
    {
        return $this->winner;
    }

    public function topScorer(): bool
    {
        return $this->topScorer;
    }
}
