<?php

declare(strict_types=1);

namespace App\Helpers\Mappers\Apisport;

final class TopScorers
{
    /**
     * @param  array<int, array<string, int>>  $players
     */
    public function __construct(private readonly array $players) {}

    public static function fromArray(mixed $response): self
    {
        $previous = 0;
        $players = [];
        if ( ! is_array($response)) {
            return new self($players);
        }
        foreach ($response as $player) {
            if ( ! isset($player['statistics'][0]['goals']) || ! is_array($player['statistics'][0]['goals'])) {
                break;
            }
            $goalsTotal = $player['statistics'][0]['goals']['total'];
            if ( ! is_int($goalsTotal)) {
                break;
            }
            if ($previous > $goalsTotal) {
                break;
            }
            if ( ! is_array($player['player']) || ! isset($player['player']['id'])) {
                continue;
            }
            $players[] = [
                'id' => $player['player']['id'],
            ];
            $previous = $goalsTotal;
        }

        return new self($players);
    }

    /**
     * @return array<int, array<string, int>>
     */
    public function toArray(): array
    {
        return $this->players;
    }
}
