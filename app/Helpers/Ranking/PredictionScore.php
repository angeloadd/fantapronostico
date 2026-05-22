<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

final readonly class PredictionScore
{
    private const RESULT_POINTS = 4;

    private const SIGN_POINTS = 1;

    private const SCORER_POINTS = 2;

    public function __construct(
        public bool $sign,
        public bool $result,
        public bool $homeScorer,
        public bool $awayScorer,
        public bool $isFinal,
        public int $timestamp
    ) {}

    public function total(): int
    {
        return ($this->result * self::RESULT_POINTS) +
            ($this->sign * self::SIGN_POINTS) +
            ($this->homeScorer * self::SCORER_POINTS) +
            ($this->awayScorer * self::SCORER_POINTS);
    }
}
