<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Champion;
use App\Models\Game;
use App\Models\Player;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use DateTimeInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class PredictionsSeeder extends Seeder
{
    public function run(): void
    {
        //        User::factory(40)->create();
        //        User::create([
        //            'email' => 'ciao@ciao.com',
        //            'name' => 'Angelo',
        //            'password' => Hash::make('123123123'),
        //        ]);
        //        $this->now = now()->subDays(5);
        //        $this->tournament = Tournament::factory()->euro()->create([
        //            'final_started_at' => $this->createDateFromDelayInDays(28, 21),
        //        ]);
        //
        $users = User::all();
        League::first()?->users()->attach($users, ['status' => 'accepted']);

        //        $users->each(function (User $user): void {
        //            $teams = Team::all();
        //            $players = Player::all();
        //            Champion::create([
        //                'user_id' => $user->id,
        //                'team_id' => $teams->random()->id,
        //                'player_id' => $players->random()->id,
        //            ]);
        //        });

        Game::all()->each(function (Game $game) use ($users): void {
            if (now()->gte($game->started_at)) {
                if ($game->home_team && $game->away_team) {
                    $users->each(function (User $user) use ($game): void {
                        $homeScorers = $game->home_team->players->map(fn (Player $player): int => $player->id);
                        $homeScorers->add(0);
                        $homeScorers->add(-1);
                        $awayScorers = $game->away_team->players->map(fn (Player $player): int => $player->id);
                        $awayScorers->add(0);
                        $awayScorers->add(-1);
                        Prediction::factory([
                            'user_id' => $user->id,
                            'game_id' => $game->id,
                            'home_scorer_id' => $homeScorers->random(),
                            'away_scorer_id' => $awayScorers->random(),
                        ])->create();
                    });
                }
            }
        });
    }

//    public function createDateFromDelayInDays(int $delayInDays, int $hour): DateTimeInterface
//    {
//        $time = Carbon::create(
//            $this->now->year,
//            $this->now->month,
//            $this->now->day + $delayInDays,
//            $hour,
//            timezone: 'Europe/Rome'
//        );
//
//        if ( ! $time) {
//            $time = now();
//        }
//
//        return $time;
//    }
}
