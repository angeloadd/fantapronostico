<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Champion;
use App\Models\Game;
use App\Models\Player;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use App\Modules\Tournament\Models\Team;
use DateTimeInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

final class GroupePhaseSeeder extends Seeder
{
    public const CALENDAR = [
        [
            'day' => 1,
            'type' => 'Group A',
            'hour' => 21,
        ],
        [
            'day' => 2,
            'type' => 'Group A',
            'hour' => 15,
        ],
        [
            'day' => 2,
            'type' => 'Group B',
            'hour' => 18,
        ],
        [
            'day' => 2,
            'type' => 'Group B',
            'hour' => 21,
        ],
        [
            'day' => 3,
            'type' => 'Group C',
            'hour' => 15,
        ],
        [
            'day' => 3,
            'type' => 'Group C',
            'hour' => 18,
        ],
        [
            'day' => 3,
            'type' => 'Group D',
            'hour' => 21,
        ],
        [
            'day' => 4,
            'type' => 'Group D',
            'hour' => 15,
        ],
        [
            'day' => 4,
            'type' => 'Group E',
            'hour' => 18,
        ],
        [
            'day' => 4,
            'type' => 'Group E',
            'hour' => 21,
        ],
        [
            'day' => 5,
            'type' => 'Group F',
            'hour' => 15,
        ],
        [
            'day' => 5,
            'type' => 'Group F',
            'hour' => 18,
        ],
        [
            'day' => 5,
            'type' => 'Group A',
            'hour' => 21,
        ],
        [
            'day' => 6,
            'type' => 'Group A',
            'hour' => 15,
        ],
        [
            'day' => 6,
            'type' => 'Group B',
            'hour' => 18,
        ],
        [
            'day' => 6,
            'type' => 'Group B',
            'hour' => 21,
        ],
        [
            'day' => 7,
            'type' => 'Group C',
            'hour' => 15,
        ],
        [
            'day' => 7,
            'type' => 'Group C',
            'hour' => 18,
        ],
        [
            'day' => 7,
            'type' => 'Group D',
            'hour' => 21,
        ],
        [
            'day' => 8,
            'type' => 'Group D',
            'hour' => 15,
        ],
        [
            'day' => 8,
            'type' => 'Group E',
            'hour' => 18,
        ],
        [
            'day' => 8,
            'type' => 'Group E',
            'hour' => 21,
        ],
        [
            'day' => 9,
            'type' => 'Group F',
            'hour' => 18,
        ],
        [
            'day' => 9,
            'type' => 'Group F',
            'hour' => 21,
        ],
        [
            'day' => 10,
            'type' => 'Group A',
            'hour' => 18,
        ],
        [
            'day' => 10,
            'type' => 'Group A',
            'hour' => 18,
        ],
        [
            'day' => 10,
            'type' => 'Group B',
            'hour' => 21,
        ],
        [
            'day' => 10,
            'type' => 'Group B',
            'hour' => 21,
        ],
        [
            'day' => 11,
            'type' => 'Group C',
            'hour' => 18,
        ],
        [
            'day' => 11,
            'type' => 'Group C',
            'hour' => 18,
        ],
        [
            'day' => 11,
            'type' => 'Group D',
            'hour' => 21,
        ],
        [
            'day' => 11,
            'type' => 'Group D',
            'hour' => 21,
        ],
        [
            'day' => 12,
            'type' => 'Group E',
            'hour' => 18,
        ],
        [
            'day' => 12,
            'type' => 'Group E',
            'hour' => 18,
        ],
        [
            'day' => 12,
            'type' => 'Group F',
            'hour' => 21,
        ],
        [
            'day' => 12,
            'type' => 'Group F',
            'hour' => 21,
        ],
        [
            'day' => 16,
            'type' => 'Round of 16',
            'hour' => 18,
        ],
        [
            'day' => 16,
            'type' => 'Round of 16',
            'hour' => 21,
        ],
        [
            'day' => 17,
            'type' => 'Round of 16',
            'hour' => 18,
        ],
        [
            'day' => 17,
            'type' => 'Round of 16',
            'hour' => 21,
        ],
        [
            'day' => 18,
            'type' => 'Round of 16',
            'hour' => 18,
        ],
        [
            'day' => 18,
            'type' => 'Round of 16',
            'hour' => 21,
        ],
        [
            'day' => 19,
            'type' => 'Round of 16',
            'hour' => 18,
        ],
        [
            'day' => 19,
            'type' => 'Round of 16',
            'hour' => 21,
        ],
        [
            'day' => 21,
            'type' => 'Quarter-Final',
            'hour' => 18,
        ],
        [
            'day' => 21,
            'type' => 'Quarter-Final',
            'hour' => 21,
        ],
        [
            'day' => 22,
            'type' => 'Quarter-Final',
            'hour' => 18,
        ],
        [
            'day' => 22,
            'type' => 'Quarter-Final',
            'hour' => 21,
        ],
        [
            'day' => 25,
            'type' => 'Semi-Final',
            'hour' => 18,
        ],
        [
            'day' => 25,
            'type' => 'Semi-Final',
            'hour' => 21,
        ],
        [
            'day' => 27,
            'type' => 'Final 3/4',
            'hour' => 21,
        ],
        [
            'day' => 28,
            'type' => 'Final',
            'hour' => 21,
        ],
    ];

    private Tournament $tournament;

    private Carbon $now;

    public function run(): void
    {
        User::factory(40)->create();
        User::create([
            'email' => 'ciao@ciao.com',
            'name' => 'Angelo',
            'password' => Hash::make('123123123'),
        ]);
        $this->now = now()->subDays(5);
        $this->tournament = Tournament::factory()->euro()->create([
            'final_started_at' => $this->createDateFromDelayInDays(28, 21),
        ]);

        Team::factory(24)
            ->national()
            ->hasAttached($this->tournament)
            ->create()
            ->each(function (Team $team): void {
                Player::factory(24)
                    ->for($team, 'national')
                    ->hasAttached($this->tournament)
                    ->create();
            });

        $teams = Team::all();

        $this->createGame($teams->get(0), $teams->get(1), 0);
        $this->createGame($teams->get(2), $teams->get(3), 1);
        $this->createGame($teams->get(4), $teams->get(5), 2);
        $this->createGame($teams->get(6), $teams->get(7), 3);
        $this->createGame($teams->get(8), $teams->get(9), 4);
        $this->createGame($teams->get(10), $teams->get(11), 5);
        $this->createGame($teams->get(12), $teams->get(13), 6);
        $this->createGame($teams->get(14), $teams->get(15), 7);
        $this->createGame($teams->get(16), $teams->get(17), 8);
        $this->createGame($teams->get(18), $teams->get(19), 9);
        $this->createGame($teams->get(20), $teams->get(21), 10);
        $this->createGame($teams->get(22), $teams->get(23), 11);

        $this->createGame($teams->get(3), $teams->get(0), 12);
        $this->createGame($teams->get(1), $teams->get(2), 13);
        $this->createGame($teams->get(7), $teams->get(4), 14);
        $this->createGame($teams->get(5), $teams->get(6), 15);
        $this->createGame($teams->get(11), $teams->get(8), 16);
        $this->createGame($teams->get(9), $teams->get(10), 17);
        $this->createGame($teams->get(15), $teams->get(12), 18);
        $this->createGame($teams->get(13), $teams->get(14), 19);
        $this->createGame($teams->get(19), $teams->get(16), 20);
        $this->createGame($teams->get(17), $teams->get(18), 21);
        $this->createGame($teams->get(23), $teams->get(20), 22);
        $this->createGame($teams->get(21), $teams->get(22), 23);

        $this->createGame($teams->get(0), $teams->get(2), 24);
        $this->createGame($teams->get(1), $teams->get(3), 25);
        $this->createGame($teams->get(4), $teams->get(6), 26);
        $this->createGame($teams->get(5), $teams->get(7), 27);
        $this->createGame($teams->get(8), $teams->get(10), 28);
        $this->createGame($teams->get(9), $teams->get(11), 29);
        $this->createGame($teams->get(12), $teams->get(14), 30);
        $this->createGame($teams->get(13), $teams->get(15), 31);
        $this->createGame($teams->get(16), $teams->get(18), 32);
        $this->createGame($teams->get(17), $teams->get(19), 33);
        $this->createGame($teams->get(20), $teams->get(22), 34);
        $this->createGame($teams->get(21), $teams->get(23), 35);

        $users = User::all();

        $users->each(function (User $user): void {
            $teams = Team::all();
            $players = Player::all();
            Champion::create([
                'user_id' => $user->id,
                'team_id' => $teams->random()->id,
                'player_id' => $players->random()->id,
            ]);
        });

        Game::all()->each(function (Game $game) use ($users): void {
            if (now()->gte($game->started_at)) {
                $users->each(function (User $user) use ($game): void {
                    if ($game->home_team && $game->away_team) {
                        Prediction::factory([
                            'user_id' => $user->id,
                            'game_id' => $game->id,
                            'home_scorer_id' => $game->home_team->players->random()->id,
                            'away_scorer_id' => $game->away_team->players->random()->id,
                        ])->create();
                    }
                });

                $game->update([
                    'status' => 'finished',
                ]);

                $game->goals()->create([
                    'player_id' => $game->teams->random()->players->random()->id,
                    'scored_at' => now(),
                ]);

            }
        });
    }

    public function createGame(?Team $homeTeam, ?Team $awayTeam, int $gameNumber): void
    {
        if ( ! isset($homeTeam, $awayTeam)) {
            return;
        }
        $gameMeta = self::CALENDAR[$gameNumber];
        $game = Game::factory(state: [
            'started_at' => $this->createDateFromDelayInDays($gameMeta['day'], $gameMeta['hour']),
            'stage' => $gameMeta['type'],
            'status' => 'not_started',
        ])->for($this->tournament)
            ->hasAttached($homeTeam, ['is_away' => 0])
            ->hasAttached($awayTeam, ['is_away' => 1])
            ->create();

        $homeTeam->players->map(static fn (Player $player) => $player->games()->attach($game));
        $awayTeam->players->map(static fn (Player $player) => $player->games()->attach($game));
    }

    public function createDateFromDelayInDays(int $delayInDays, int $hour): DateTimeInterface
    {
        $time = Carbon::create(
            $this->now->year,
            $this->now->month,
            $this->now->day + $delayInDays,
            $hour,
            timezone: 'Europe/Rome'
        );

        if ( ! $time) {
            $time = now();
        }

        return $time;
    }
}
