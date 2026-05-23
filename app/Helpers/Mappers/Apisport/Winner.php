<?php

declare(strict_types=1);

namespace App\Helpers\Mappers\Apisport;

final class Winner
{
    public function __construct(private ?int $winnerTeamId) {}

    public static function fromArray(array $response): self
    {
        $final = array_filter(
            $response,
            static fn ($item) => 'Final' === $item['league']['round']
        );

        $game = current($final);

        if (!isset($game)) {
            return new self(null);
        }

        return $game['teams']['home']['winner'] ?
            new self($game['teams']['home']['id']) :
            new self($game['teams']['away']['id']);
    }

    public function toInt(): ?int
    {
        return $this->winnerTeamId;
    }
}
