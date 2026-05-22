# Dev Phase Seeders Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create four standalone Laravel seeders that simulate different UEFA Euro 2024 tournament phases with real team/player data, named users with known credentials, and fully dynamic timestamps relative to `now()`.

**Architecture:** An abstract `DevBaseSeeder` holds all shared constants (24 real Euro 2024 teams, 72 real players, 52-game schedule) and protected helper methods. Four concrete phase seeders extend it and compose their state using those helpers. Each seeder is fully self-contained and runnable via `php artisan db:seed --class=DevXxxSeeder` after a `migrate:fresh`.

**Tech Stack:** Laravel Eloquent, Carbon, DB facade, PHP 8.1+, `database/seeders/`

---

## File Map

| File | Purpose |
|---|---|
| `database/seeders/DevBaseSeeder.php` | Abstract base: TEAMS/PLAYERS/SCHEDULE constants + all shared helper methods |
| `database/seeders/DevBeforeStartSeeder.php` | Phase A: all 52 games `not_started`, some users missing champion pick |
| `database/seeders/DevGroupPhaseSeeder.php` | Phase B: games 0–23 `finished` (matchdays 1+2), rest upcoming |
| `database/seeders/DevKnockoutPhaseSeeder.php` | Phase C: games 0–39 `finished` (all group + 4 R16), rest upcoming |
| `database/seeders/DevFinalSeeder.php` | Phase D: all 52 games `finished`, full ranks, winner/top-scorer bonuses |

---

## Known Users (all phases)

| Email | Password | Role |
|---|---|---|
| `admin@fp.test` | `password` | `admin` (global, no league_id) |
| `mod@fp.test` | `password` | `mod` (league-scoped) |
| `user1@fp.test` | `password` | regular member |
| `user2@fp.test` | `password` | regular member |
| `user3@fp.test` | `password` | regular member (no champion in Phase A) |
| 10 faker users | `password` | regular members (5 without champion in Phase A) |

---

### Task 1: DevBaseSeeder

**Files:**
- Create: `database/seeders/DevBaseSeeder.php`

- [ ] **Step 1: Create the file**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Champion;
use App\Models\Game;
use App\Models\GameGoal;
use App\Models\Player;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Modules\Auth\Enums\RoleEnum;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class DevBaseSeeder extends Seeder
{
    // Group A=0-3, B=4-7, C=8-11, D=12-15, E=16-19, F=20-23
    protected const TEAMS = [
        ['name' => 'Germany',        'code' => 'GER', 'api_id' => 25,   'logo' => 'https://media.api-sports.io/football/teams/25.png'],
        ['name' => 'Scotland',       'code' => 'SCO', 'api_id' => 1108, 'logo' => 'https://media.api-sports.io/football/teams/1108.png'],
        ['name' => 'Hungary',        'code' => 'HUN', 'api_id' => 769,  'logo' => 'https://media.api-sports.io/football/teams/769.png'],
        ['name' => 'Switzerland',    'code' => 'SUI', 'api_id' => 15,   'logo' => 'https://media.api-sports.io/football/teams/15.png'],
        ['name' => 'Spain',          'code' => 'ESP', 'api_id' => 9,    'logo' => 'https://media.api-sports.io/football/teams/9.png'],
        ['name' => 'Croatia',        'code' => 'CRO', 'api_id' => 3,    'logo' => 'https://media.api-sports.io/football/teams/3.png'],
        ['name' => 'Italy',          'code' => 'ITA', 'api_id' => 768,  'logo' => 'https://media.api-sports.io/football/teams/768.png'],
        ['name' => 'Albania',        'code' => 'ALB', 'api_id' => 778,  'logo' => 'https://media.api-sports.io/football/teams/778.png'],
        ['name' => 'England',        'code' => 'ENG', 'api_id' => 10,   'logo' => 'https://media.api-sports.io/football/teams/10.png'],
        ['name' => 'Serbia',         'code' => 'SRB', 'api_id' => 14,   'logo' => 'https://media.api-sports.io/football/teams/14.png'],
        ['name' => 'Denmark',        'code' => 'DEN', 'api_id' => 21,   'logo' => 'https://media.api-sports.io/football/teams/21.png'],
        ['name' => 'Slovenia',       'code' => 'SLO', 'api_id' => 1091, 'logo' => 'https://media.api-sports.io/football/teams/1091.png'],
        ['name' => 'France',         'code' => 'FRA', 'api_id' => 2,    'logo' => 'https://media.api-sports.io/football/teams/2.png'],
        ['name' => 'Austria',        'code' => 'AUT', 'api_id' => 775,  'logo' => 'https://media.api-sports.io/football/teams/775.png'],
        ['name' => 'Netherlands',    'code' => 'NED', 'api_id' => 1118, 'logo' => 'https://media.api-sports.io/football/teams/1118.png'],
        ['name' => 'Poland',         'code' => 'POL', 'api_id' => 24,   'logo' => 'https://media.api-sports.io/football/teams/24.png'],
        ['name' => 'Belgium',        'code' => 'BEL', 'api_id' => 1,    'logo' => 'https://media.api-sports.io/football/teams/1.png'],
        ['name' => 'Slovakia',       'code' => 'SVK', 'api_id' => 773,  'logo' => 'https://media.api-sports.io/football/teams/773.png'],
        ['name' => 'Romania',        'code' => 'ROU', 'api_id' => 774,  'logo' => 'https://media.api-sports.io/football/teams/774.png'],
        ['name' => 'Ukraine',        'code' => 'UKR', 'api_id' => 772,  'logo' => 'https://media.api-sports.io/football/teams/772.png'],
        ['name' => 'Turkey',         'code' => 'TUR', 'api_id' => 777,  'logo' => 'https://media.api-sports.io/football/teams/777.png'],
        ['name' => 'Georgia',        'code' => 'GEO', 'api_id' => 1104, 'logo' => 'https://media.api-sports.io/football/teams/1104.png'],
        ['name' => 'Portugal',       'code' => 'POR', 'api_id' => 27,   'logo' => 'https://media.api-sports.io/football/teams/27.png'],
        ['name' => 'Czech Republic', 'code' => 'CZE', 'api_id' => 770,  'logo' => 'https://media.api-sports.io/football/teams/770.png'],
    ];

    // 3 players per team, indexed by TEAMS index
    protected const PLAYERS = [
        0  => [['first_name' => 'Manuel',    'last_name' => 'Neuer',            'displayed_name' => 'M. Neuer'],
               ['first_name' => 'Thomas',    'last_name' => 'Müller',           'displayed_name' => 'T. Müller'],
               ['first_name' => 'Joshua',    'last_name' => 'Kimmich',          'displayed_name' => 'J. Kimmich']],
        1  => [['first_name' => 'Andrew',    'last_name' => 'Robertson',        'displayed_name' => 'A. Robertson'],
               ['first_name' => 'Scott',     'last_name' => 'McTominay',        'displayed_name' => 'S. McTominay'],
               ['first_name' => 'Lyndon',    'last_name' => 'Dykes',            'displayed_name' => 'L. Dykes']],
        2  => [['first_name' => 'Dominik',   'last_name' => 'Szoboszlai',       'displayed_name' => 'D. Szoboszlai'],
               ['first_name' => 'Roland',    'last_name' => 'Varga',            'displayed_name' => 'R. Varga'],
               ['first_name' => 'Roland',    'last_name' => 'Sallai',           'displayed_name' => 'R. Sallai']],
        3  => [['first_name' => 'Xherdan',   'last_name' => 'Shaqiri',          'displayed_name' => 'X. Shaqiri'],
               ['first_name' => 'Manuel',    'last_name' => 'Akanji',           'displayed_name' => 'M. Akanji'],
               ['first_name' => 'Breel',     'last_name' => 'Embolo',           'displayed_name' => 'B. Embolo']],
        4  => [['first_name' => 'Lamine',    'last_name' => 'Yamal',            'displayed_name' => 'L. Yamal'],
               ['first_name' => 'Alvaro',    'last_name' => 'Morata',           'displayed_name' => 'A. Morata'],
               ['first_name' => 'Pedro',     'last_name' => 'González',         'displayed_name' => 'Pedri']],
        5  => [['first_name' => 'Luka',      'last_name' => 'Modric',           'displayed_name' => 'L. Modric'],
               ['first_name' => 'Josko',     'last_name' => 'Gvardiol',         'displayed_name' => 'J. Gvardiol'],
               ['first_name' => 'Andrej',    'last_name' => 'Budimir',          'displayed_name' => 'A. Budimir']],
        6  => [['first_name' => 'Gianluigi', 'last_name' => 'Donnarumma',       'displayed_name' => 'G. Donnarumma'],
               ['first_name' => 'Nicolo',    'last_name' => 'Barella',          'displayed_name' => 'N. Barella'],
               ['first_name' => 'Mateo',     'last_name' => 'Retegui',          'displayed_name' => 'M. Retegui']],
        7  => [['first_name' => 'Elseid',    'last_name' => 'Bajrami',          'displayed_name' => 'E. Bajrami'],
               ['first_name' => 'Kristjan',  'last_name' => 'Asllani',          'displayed_name' => 'K. Asllani'],
               ['first_name' => 'Armando',   'last_name' => 'Broja',            'displayed_name' => 'A. Broja']],
        8  => [['first_name' => 'Harry',     'last_name' => 'Kane',             'displayed_name' => 'H. Kane'],
               ['first_name' => 'Bukayo',    'last_name' => 'Saka',             'displayed_name' => 'B. Saka'],
               ['first_name' => 'Jude',      'last_name' => 'Bellingham',       'displayed_name' => 'J. Bellingham']],
        9  => [['first_name' => 'Dusan',     'last_name' => 'Vlahovic',         'displayed_name' => 'D. Vlahovic'],
               ['first_name' => 'Dusan',     'last_name' => 'Tadic',            'displayed_name' => 'D. Tadic'],
               ['first_name' => 'Sergej',    'last_name' => 'Milinkovic-Savic', 'displayed_name' => 'S. Milinkovic-Savic']],
        10 => [['first_name' => 'Christian', 'last_name' => 'Eriksen',          'displayed_name' => 'C. Eriksen'],
               ['first_name' => 'Pierre',    'last_name' => 'Hojbjerg',         'displayed_name' => 'P. Hojbjerg'],
               ['first_name' => 'Kasper',    'last_name' => 'Schmeichel',       'displayed_name' => 'K. Schmeichel']],
        11 => [['first_name' => 'Benjamin',  'last_name' => 'Sesko',            'displayed_name' => 'B. Sesko'],
               ['first_name' => 'Jan',       'last_name' => 'Oblak',            'displayed_name' => 'J. Oblak'],
               ['first_name' => 'Jaka',      'last_name' => 'Bijol',            'displayed_name' => 'J. Bijol']],
        12 => [['first_name' => 'Kylian',    'last_name' => 'Mbappe',           'displayed_name' => 'K. Mbappe'],
               ['first_name' => 'Antoine',   'last_name' => 'Griezmann',        'displayed_name' => 'A. Griezmann'],
               ['first_name' => 'Eduardo',   'last_name' => 'Camavinga',        'displayed_name' => 'E. Camavinga']],
        13 => [['first_name' => 'Marko',     'last_name' => 'Arnautovic',       'displayed_name' => 'M. Arnautovic'],
               ['first_name' => 'Marcel',    'last_name' => 'Sabitzer',         'displayed_name' => 'M. Sabitzer'],
               ['first_name' => 'David',     'last_name' => 'Alaba',            'displayed_name' => 'D. Alaba']],
        14 => [['first_name' => 'Virgil',    'last_name' => 'van Dijk',         'displayed_name' => 'V. van Dijk'],
               ['first_name' => 'Memphis',   'last_name' => 'Depay',            'displayed_name' => 'M. Depay'],
               ['first_name' => 'Cody',      'last_name' => 'Gakpo',            'displayed_name' => 'C. Gakpo']],
        15 => [['first_name' => 'Robert',    'last_name' => 'Lewandowski',      'displayed_name' => 'R. Lewandowski'],
               ['first_name' => 'Sebastian', 'last_name' => 'Szymanski',        'displayed_name' => 'S. Szymanski'],
               ['first_name' => 'Nicola',    'last_name' => 'Zalewski',         'displayed_name' => 'N. Zalewski']],
        16 => [['first_name' => 'Kevin',     'last_name' => 'De Bruyne',        'displayed_name' => 'K. De Bruyne'],
               ['first_name' => 'Romelu',    'last_name' => 'Lukaku',           'displayed_name' => 'R. Lukaku'],
               ['first_name' => 'Youri',     'last_name' => 'Tielemans',        'displayed_name' => 'Y. Tielemans']],
        17 => [['first_name' => 'Ondrej',    'last_name' => 'Duda',             'displayed_name' => 'O. Duda'],
               ['first_name' => 'Milan',     'last_name' => 'Skriniar',         'displayed_name' => 'M. Skriniar'],
               ['first_name' => 'Stanislav', 'last_name' => 'Lobotka',          'displayed_name' => 'S. Lobotka']],
        18 => [['first_name' => 'Nicolae',   'last_name' => 'Stanciu',          'displayed_name' => 'N. Stanciu'],
               ['first_name' => 'George',    'last_name' => 'Puscas',           'displayed_name' => 'G. Puscas'],
               ['first_name' => 'Denis',     'last_name' => 'Dragus',           'displayed_name' => 'D. Dragus']],
        19 => [['first_name' => 'Mykhailo',  'last_name' => 'Mudryk',           'displayed_name' => 'M. Mudryk'],
               ['first_name' => 'Oleksandr', 'last_name' => 'Zinchenko',        'displayed_name' => 'O. Zinchenko'],
               ['first_name' => 'Artem',     'last_name' => 'Dovbyk',           'displayed_name' => 'A. Dovbyk']],
        20 => [['first_name' => 'Hakan',     'last_name' => 'Calhanoglu',       'displayed_name' => 'H. Calhanoglu'],
               ['first_name' => 'Arda',      'last_name' => 'Guler',            'displayed_name' => 'A. Guler'],
               ['first_name' => 'Kenan',     'last_name' => 'Yildiz',           'displayed_name' => 'K. Yildiz']],
        21 => [['first_name' => 'Khvicha',   'last_name' => 'Kvaratskhelia',    'displayed_name' => 'K. Kvaratskhelia'],
               ['first_name' => 'Georges',   'last_name' => 'Mikautadze',       'displayed_name' => 'G. Mikautadze'],
               ['first_name' => 'Giorgi',    'last_name' => 'Davitashvili',     'displayed_name' => 'G. Davitashvili']],
        22 => [['first_name' => 'Cristiano', 'last_name' => 'Ronaldo',          'displayed_name' => 'C. Ronaldo'],
               ['first_name' => 'Bruno',     'last_name' => 'Fernandes',        'displayed_name' => 'B. Fernandes'],
               ['first_name' => 'Joao',      'last_name' => 'Felix',            'displayed_name' => 'J. Felix']],
        23 => [['first_name' => 'Patrik',    'last_name' => 'Schick',           'displayed_name' => 'P. Schick'],
               ['first_name' => 'Tomas',     'last_name' => 'Soucek',           'displayed_name' => 'T. Soucek'],
               ['first_name' => 'Vladimir',  'last_name' => 'Coufal',           'displayed_name' => 'V. Coufal']],
    ];

    // 52 game slots: [stage, home TEAMS index, away TEAMS index]
    // 0-35: group stage | 36-43: R16 | 44-47: QF | 48-49: SF | 50: 3rd/4th | 51: Final
    // Spain (index 4) wins; Yamal (Spain player 0) is top scorer
    protected const SCHEDULE = [
        // Group A MD1
        ['stage' => 'Group A',      'home' => 0,  'away' => 1],
        ['stage' => 'Group A',      'home' => 2,  'away' => 3],
        // Group B MD1
        ['stage' => 'Group B',      'home' => 4,  'away' => 5],
        ['stage' => 'Group B',      'home' => 6,  'away' => 7],
        // Group C MD1
        ['stage' => 'Group C',      'home' => 8,  'away' => 9],
        ['stage' => 'Group C',      'home' => 10, 'away' => 11],
        // Group D MD1
        ['stage' => 'Group D',      'home' => 12, 'away' => 13],
        ['stage' => 'Group D',      'home' => 14, 'away' => 15],
        // Group E MD1
        ['stage' => 'Group E',      'home' => 16, 'away' => 17],
        ['stage' => 'Group E',      'home' => 18, 'away' => 19],
        // Group F MD1
        ['stage' => 'Group F',      'home' => 20, 'away' => 21],
        ['stage' => 'Group F',      'home' => 22, 'away' => 23],
        // Group A MD2
        ['stage' => 'Group A',      'home' => 0,  'away' => 2],
        ['stage' => 'Group A',      'home' => 1,  'away' => 3],
        // Group B MD2
        ['stage' => 'Group B',      'home' => 4,  'away' => 6],
        ['stage' => 'Group B',      'home' => 5,  'away' => 7],
        // Group C MD2
        ['stage' => 'Group C',      'home' => 8,  'away' => 10],
        ['stage' => 'Group C',      'home' => 9,  'away' => 11],
        // Group D MD2
        ['stage' => 'Group D',      'home' => 12, 'away' => 14],
        ['stage' => 'Group D',      'home' => 13, 'away' => 15],
        // Group E MD2
        ['stage' => 'Group E',      'home' => 16, 'away' => 18],
        ['stage' => 'Group E',      'home' => 17, 'away' => 19],
        // Group F MD2
        ['stage' => 'Group F',      'home' => 20, 'away' => 22],
        ['stage' => 'Group F',      'home' => 21, 'away' => 23],
        // Group A MD3
        ['stage' => 'Group A',      'home' => 0,  'away' => 3],
        ['stage' => 'Group A',      'home' => 1,  'away' => 2],
        // Group B MD3
        ['stage' => 'Group B',      'home' => 4,  'away' => 7],
        ['stage' => 'Group B',      'home' => 5,  'away' => 6],
        // Group C MD3
        ['stage' => 'Group C',      'home' => 8,  'away' => 11],
        ['stage' => 'Group C',      'home' => 9,  'away' => 10],
        // Group D MD3
        ['stage' => 'Group D',      'home' => 12, 'away' => 15],
        ['stage' => 'Group D',      'home' => 13, 'away' => 14],
        // Group E MD3
        ['stage' => 'Group E',      'home' => 16, 'away' => 19],
        ['stage' => 'Group E',      'home' => 17, 'away' => 18],
        // Group F MD3
        ['stage' => 'Group F',      'home' => 20, 'away' => 23],
        ['stage' => 'Group F',      'home' => 21, 'away' => 22],
        // Round of 16 (indices 36-43)
        ['stage' => 'Round of 16',  'home' => 4,  'away' => 5],   // Spain vs Croatia
        ['stage' => 'Round of 16',  'home' => 0,  'away' => 10],  // Germany vs Denmark
        ['stage' => 'Round of 16',  'home' => 12, 'away' => 16],  // France vs Belgium
        ['stage' => 'Round of 16',  'home' => 22, 'away' => 11],  // Portugal vs Slovenia
        ['stage' => 'Round of 16',  'home' => 8,  'away' => 17],  // England vs Slovakia
        ['stage' => 'Round of 16',  'home' => 3,  'away' => 6],   // Switzerland vs Italy
        ['stage' => 'Round of 16',  'home' => 14, 'away' => 18],  // Netherlands vs Romania
        ['stage' => 'Round of 16',  'home' => 20, 'away' => 13],  // Turkey vs Austria
        // Quarter-Finals (indices 44-47)
        ['stage' => 'Quarter-Final','home' => 4,  'away' => 0],   // Spain vs Germany
        ['stage' => 'Quarter-Final','home' => 12, 'away' => 22],  // France vs Portugal
        ['stage' => 'Quarter-Final','home' => 8,  'away' => 3],   // England vs Switzerland
        ['stage' => 'Quarter-Final','home' => 14, 'away' => 20],  // Netherlands vs Turkey
        // Semi-Finals (indices 48-49)
        ['stage' => 'Semi-Final',   'home' => 4,  'away' => 12],  // Spain vs France
        ['stage' => 'Semi-Final',   'home' => 8,  'away' => 14],  // England vs Netherlands
        // Final 3/4 (index 50)
        ['stage' => 'Final 3/4',    'home' => 12, 'away' => 14],  // France vs Netherlands
        // Final (index 51) — Spain wins
        ['stage' => 'Final',        'home' => 4,  'away' => 8],   // Spain vs England
    ];

    // Spain (TEAMS index 4) wins; Yamal (PLAYERS[4][0]) is top scorer
    protected const WINNER_TEAM_INDEX    = 4;
    protected const TOP_SCORER_TEAM_INDEX = 4;
    protected const TOP_SCORER_PLAYER_LOCAL_INDEX = 0;

    /**
     * Places game $gameIndex in time relative to $now.
     * Finished games (index < $finishedCount) spread backwards; upcoming games spread forward.
     * Two games per day at 18:00 and 21:00, maintaining chronological order.
     */
    protected function computeStartedAt(int $gameIndex, int $finishedCount, Carbon $now): Carbon
    {
        if ($gameIndex < $finishedCount) {
            $daysAgo = (int)(($finishedCount - 1 - $gameIndex) / 2) + 1;
            $hour    = 18 + ($gameIndex % 2) * 3;

            return $now->copy()->subDays($daysAgo)->setHour($hour)->setMinute(0)->setSecond(0);
        }

        $futureIndex = $gameIndex - $finishedCount;
        $daysFromNow = (int)($futureIndex / 2) + 2;
        $hour        = 18 + ($futureIndex % 2) * 3;

        return $now->copy()->addDays($daysFromNow)->setHour($hour)->setMinute(0)->setSecond(0);
    }

    protected function createTournament(Carbon $start, Carbon $finalStart): Tournament
    {
        return Tournament::create([
            'country'          => 'World',
            'name'             => 'UEFA Euro Cup',
            'logo'             => 'https://media.api-sports.io/football/leagues/4.png',
            'is_cup'           => true,
            'season'           => 2024,
            'api_id'           => 4,
            'started_at'       => $start,
            'final_started_at' => $finalStart,
        ]);
    }

    protected function createLeague(Tournament $tournament): League
    {
        return $tournament->leagues()->create(['name' => 'Fantapronostico2024']);
    }

    /**
     * Creates 24 national teams with 3 players each, attaches all to tournament.
     * Returns a Collection indexed 0-23 matching TEAMS/SCHEDULE indices.
     */
    protected function createTeamsAndPlayers(Tournament $tournament): Collection
    {
        $teams = collect();

        foreach (self::TEAMS as $idx => $teamData) {
            $team = Team::create([
                'name'        => $teamData['name'],
                'code'        => $teamData['code'],
                'api_id'      => $teamData['api_id'],
                'logo'        => $teamData['logo'],
                'is_national' => true,
            ]);

            $tournament->teams()->attach($team->id, ['is_winner' => false]);

            foreach (self::PLAYERS[$idx] as $playerData) {
                $player = Player::create([
                    'displayed_name' => $playerData['displayed_name'],
                    'first_name'     => $playerData['first_name'],
                    'last_name'      => $playerData['last_name'],
                    'national_id'    => $team->id,
                ]);
                $tournament->players()->attach($player->id, ['is_top_scorer' => false]);
            }

            $teams->push($team->load('players'));
        }

        return $teams;
    }

    /**
     * Creates 5 named + 10 faker users, assigns roles, sets selected_league_id,
     * attaches all to the league with status=accepted.
     */
    protected function createUsers(League $league): Collection
    {
        $password = Hash::make('password');

        $namedUsers = collect([
            User::create(['name' => 'Admin User', 'email' => 'admin@fp.test', 'password' => $password, 'email_verified_at' => now()]),
            User::create(['name' => 'Mod User',   'email' => 'mod@fp.test',   'password' => $password, 'email_verified_at' => now()]),
            User::create(['name' => 'User One',   'email' => 'user1@fp.test', 'password' => $password, 'email_verified_at' => now()]),
            User::create(['name' => 'User Two',   'email' => 'user2@fp.test', 'password' => $password, 'email_verified_at' => now()]),
            User::create(['name' => 'User Three', 'email' => 'user3@fp.test', 'password' => $password, 'email_verified_at' => now()]),
        ]);

        $fakerUsers = User::factory(10)->create();
        $allUsers   = $namedUsers->merge($fakerUsers);

        $allUsers->each(function (User $user) use ($league): void {
            $user->update(['selected_league_id' => $league->id]);
            $league->users()->attach($user->id, ['status' => 'accepted']);
        });

        Role::create(['user_id' => $namedUsers[0]->id, 'role' => RoleEnum::ADMIN, 'league_id' => null]);
        Role::create(['user_id' => $namedUsers[1]->id, 'role' => RoleEnum::MOD,   'league_id' => $league->id]);

        return $allUsers;
    }

    /**
     * Creates a Game record, attaches both teams with is_away pivot, attaches all their players.
     */
    protected function createGame(
        Team $homeTeam,
        Team $awayTeam,
        string $stage,
        Carbon $startedAt,
        Tournament $tournament,
        string $status = 'not_started'
    ): Game {
        $game = Game::create([
            'tournament_id' => $tournament->id,
            'stage'         => $stage,
            'status'        => $status,
            'started_at'    => $startedAt,
        ]);

        $game->teams()->attach($homeTeam->id, ['is_away' => false]);
        $game->teams()->attach($awayTeam->id, ['is_away' => true]);

        $homeTeam->players->each(fn (Player $p) => $game->players()->attach($p->id));
        $awayTeam->players->each(fn (Player $p) => $game->players()->attach($p->id));

        return $game;
    }

    /** Creates 1–3 random goals spread across a finished game's duration. */
    protected function createGoalsForGame(Game $game, Team $homeTeam, Team $awayTeam): void
    {
        $allPlayers = $homeTeam->players->merge($awayTeam->players);
        $goalCount  = random_int(1, 3);

        for ($i = 0; $i < $goalCount; $i++) {
            GameGoal::create([
                'game_id'    => $game->id,
                'player_id'  => $allPlayers->random()->id,
                'is_autogoal' => false,
                'scored_at'  => $game->started_at->copy()->addMinutes(random_int(10, 85)),
            ]);
        }
    }

    /** Creates one prediction per user for a finished game. */
    protected function createPredictionsForGame(
        Game $game,
        Team $homeTeam,
        Team $awayTeam,
        Collection $users,
        League $league
    ): void {
        $users->each(function (User $user) use ($game, $homeTeam, $awayTeam, $league): void {
            $homeScore = random_int(0, 2);
            $awayScore = random_int(0, 2);
            $sign      = match (true) {
                $homeScore > $awayScore => '1',
                $homeScore < $awayScore => '2',
                default                => 'x',
            };

            Prediction::create([
                'user_id'        => $user->id,
                'game_id'        => $game->id,
                'league_id'      => $league->id,
                'home_score'     => $homeScore,
                'away_score'     => $awayScore,
                'sign'           => $sign,
                'home_scorer_id' => $homeScore > 0 ? $homeTeam->players->random()->id : 0,
                'away_scorer_id' => $awayScore > 0 ? $awayTeam->players->random()->id : 0,
            ]);
        });
    }

    /**
     * Creates champion picks for all users except those whose email is in $skipEmails.
     * Each user picks a random team and a random player from that team.
     * champions.created_at/updated_at are string columns, not timestamps.
     */
    protected function createChampions(Collection $users, Collection $teams, array $skipEmails = []): void
    {
        $now = now()->toDateTimeString();

        $users->each(function (User $user) use ($teams, $skipEmails, $now): void {
            if (in_array($user->email, $skipEmails, true)) {
                return;
            }
            $team = $teams->random();
            Champion::create([
                'user_id'    => $user->id,
                'team_id'    => $team->id,
                'player_id'  => $team->players->random()->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    /**
     * Inserts rank rows for each user with plausible points scaled to $finishedGamesCount.
     * Ranks table has no Eloquent model — use DB::table directly.
     */
    protected function createRanks(Collection $users, League $league, int $finishedGamesCount): void
    {
        $users->each(function (User $user) use ($league, $finishedGamesCount): void {
            $signs   = random_int(0, $finishedGamesCount);
            $results = random_int(0, (int)($signs * 0.4));
            $scorers = random_int(0, $finishedGamesCount * 2);
            $total   = $signs + $results * 2 + $scorers * 3;

            DB::table('ranks')->insert([
                'user_id'         => $user->id,
                'league_id'       => $league->id,
                'total'           => $total,
                'results'         => $results,
                'scorers'         => $scorers,
                'signs'           => $signs,
                'final_total'     => 0,
                'final_timestamp' => null,
                'winner'          => false,
                'top_scorer'      => false,
                'from'            => null,
            ]);
        });
    }
}
```

- [ ] **Step 2: Verify PHP syntax**

```bash
php -l database/seeders/DevBaseSeeder.php
```
Expected output: `No syntax errors detected in database/seeders/DevBaseSeeder.php`

- [ ] **Step 3: Commit**

```bash
git add database/seeders/DevBaseSeeder.php
git commit -m "feat: add DevBaseSeeder with Euro 2024 teams/players/schedule constants and shared helpers"
```

---

### Task 2: DevBeforeStartSeeder (Phase A)

All 52 games `not_started` in the future. user3 and 5 faker users have no champion pick yet. No predictions, goals, or ranks.

**Files:**
- Create: `database/seeders/DevBeforeStartSeeder.php`

- [ ] **Step 1: Create the file**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;

final class DevBeforeStartSeeder extends DevBaseSeeder
{
    public function run(): void
    {
        $now           = Carbon::now();
        $finishedCount = 0;

        $tournamentStart = $this->computeStartedAt(0, $finishedCount, $now);
        $finalStart      = $this->computeStartedAt(51, $finishedCount, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league     = $this->createLeague($tournament);
        $teams      = $this->createTeamsAndPlayers($tournament);
        $users      = $this->createUsers($league);

        // user3 + first 5 faker users (collection indices 5-9) have no champion pick yet
        $skipEmails = array_merge(
            ['user3@fp.test'],
            $users->slice(5)->take(5)->pluck('email')->all()
        );

        foreach (self::SCHEDULE as $i => $slot) {
            $this->createGame(
                $teams[$slot['home']],
                $teams[$slot['away']],
                $slot['stage'],
                $this->computeStartedAt($i, $finishedCount, $now),
                $tournament,
                'not_started'
            );
        }

        $this->createChampions($users, $teams, $skipEmails);
        // No predictions, goals, or ranks — tournament has not started
    }
}
```

- [ ] **Step 2: Run and spot-check**

```bash
php artisan migrate:fresh && php artisan db:seed --class=DevBeforeStartSeeder
```

```bash
php artisan tinker --execute="
use App\Models\Game;
use App\Models\Champion;
use App\Modules\Auth\Models\User;
echo 'Games: ' . Game::count() . PHP_EOL;
echo 'not_started: ' . Game::where('status','not_started')->count() . PHP_EOL;
echo 'Users: ' . User::count() . PHP_EOL;
echo 'Champions: ' . Champion::count() . PHP_EOL;
echo 'First game at: ' . Game::orderBy('started_at')->first()->started_at . PHP_EOL;
"
```
Expected:
```
Games: 52
not_started: 52
Users: 15
Champions: 9
First game at: [~2 days from now at 18:00]
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/DevBeforeStartSeeder.php
git commit -m "feat: add DevBeforeStartSeeder - phase A, all games upcoming, partial champion picks"
```

---

### Task 3: DevGroupPhaseSeeder (Phase B)

Games 0–23 `finished` (group matchdays 1 and 2). Games 24–51 `not_started`. All users have champions. Predictions and goals exist for finished games. Rank rows created.

**Files:**
- Create: `database/seeders/DevGroupPhaseSeeder.php`

- [ ] **Step 1: Create the file**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;

final class DevGroupPhaseSeeder extends DevBaseSeeder
{
    private const FINISHED_COUNT = 24;

    public function run(): void
    {
        $now = Carbon::now();

        $tournamentStart = $this->computeStartedAt(0, self::FINISHED_COUNT, $now);
        $finalStart      = $this->computeStartedAt(51, self::FINISHED_COUNT, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league     = $this->createLeague($tournament);
        $teams      = $this->createTeamsAndPlayers($tournament);
        $users      = $this->createUsers($league);

        $this->createChampions($users, $teams);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam  = $teams[$slot['home']];
            $awayTeam  = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, self::FINISHED_COUNT, $now);
            $status    = $i < self::FINISHED_COUNT ? 'finished' : 'not_started';

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, $status);

            if ('finished' === $status) {
                $this->createGoalsForGame($game, $homeTeam, $awayTeam);
                $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
            }
        }

        $this->createRanks($users, $league, self::FINISHED_COUNT);
    }
}
```

- [ ] **Step 2: Run and spot-check**

```bash
php artisan migrate:fresh && php artisan db:seed --class=DevGroupPhaseSeeder
```

```bash
php artisan tinker --execute="
use App\Models\Game;
use App\Models\Prediction;
use App\Models\GameGoal;
use Illuminate\Support\Facades\DB;
echo 'Finished: '     . Game::where('status','finished')->count() . PHP_EOL;
echo 'Not started: '  . Game::where('status','not_started')->count() . PHP_EOL;
echo 'Predictions: '  . Prediction::count() . PHP_EOL;
echo 'Goals: '        . GameGoal::count() . PHP_EOL;
echo 'Rank rows: '    . DB::table('ranks')->count() . PHP_EOL;
"
```
Expected:
```
Finished: 24
Not started: 28
Predictions: 360   (24 games × 15 users)
Goals: 24–72       (1–3 per game, random)
Rank rows: 15
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/DevGroupPhaseSeeder.php
git commit -m "feat: add DevGroupPhaseSeeder - phase B, matchdays 1-2 done with predictions and goals"
```

---

### Task 4: DevKnockoutPhaseSeeder (Phase C)

Games 0–39 `finished` (all 36 group games + first 4 Round of 16). Games 40–51 `not_started`.

**Files:**
- Create: `database/seeders/DevKnockoutPhaseSeeder.php`

- [ ] **Step 1: Create the file**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Support\Carbon;

final class DevKnockoutPhaseSeeder extends DevBaseSeeder
{
    private const FINISHED_COUNT = 40;

    public function run(): void
    {
        $now = Carbon::now();

        $tournamentStart = $this->computeStartedAt(0, self::FINISHED_COUNT, $now);
        $finalStart      = $this->computeStartedAt(51, self::FINISHED_COUNT, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league     = $this->createLeague($tournament);
        $teams      = $this->createTeamsAndPlayers($tournament);
        $users      = $this->createUsers($league);

        $this->createChampions($users, $teams);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam  = $teams[$slot['home']];
            $awayTeam  = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, self::FINISHED_COUNT, $now);
            $status    = $i < self::FINISHED_COUNT ? 'finished' : 'not_started';

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, $status);

            if ('finished' === $status) {
                $this->createGoalsForGame($game, $homeTeam, $awayTeam);
                $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
            }
        }

        $this->createRanks($users, $league, self::FINISHED_COUNT);
    }
}
```

- [ ] **Step 2: Run and spot-check**

```bash
php artisan migrate:fresh && php artisan db:seed --class=DevKnockoutPhaseSeeder
```

```bash
php artisan tinker --execute="
use App\Models\Game;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
echo 'Finished: '    . Game::where('status','finished')->count() . PHP_EOL;
echo 'Not started: ' . Game::where('status','not_started')->count() . PHP_EOL;
echo 'Predictions: ' . Prediction::count() . PHP_EOL;
echo 'Rank rows: '   . DB::table('ranks')->count() . PHP_EOL;
"
```
Expected:
```
Finished: 40
Not started: 12
Predictions: 600   (40 × 15)
Rank rows: 15
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/DevKnockoutPhaseSeeder.php
git commit -m "feat: add DevKnockoutPhaseSeeder - phase C, all group + partial R16 done"
```

---

### Task 5: DevFinalSeeder (Phase D)

All 52 games `finished`. Spain marked as tournament winner (`team_tournament.is_winner = true`). Yamal marked as top scorer (`player_tournament.is_top_scorer = true`). Users whose champion pick matched get winner (+15) and/or top-scorer (+10) bonuses on their rank row.

**Files:**
- Create: `database/seeders/DevFinalSeeder.php`

- [ ] **Step 1: Create the file**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Champion;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class DevFinalSeeder extends DevBaseSeeder
{
    private const FINISHED_COUNT = 52;

    public function run(): void
    {
        $now = Carbon::now();

        $tournamentStart = $this->computeStartedAt(0, self::FINISHED_COUNT, $now);
        $finalStart      = $this->computeStartedAt(51, self::FINISHED_COUNT, $now);

        $tournament = $this->createTournament($tournamentStart, $finalStart);
        $league     = $this->createLeague($tournament);
        $teams      = $this->createTeamsAndPlayers($tournament);
        $users      = $this->createUsers($league);

        $this->createChampions($users, $teams);

        foreach (self::SCHEDULE as $i => $slot) {
            $homeTeam  = $teams[$slot['home']];
            $awayTeam  = $teams[$slot['away']];
            $startedAt = $this->computeStartedAt($i, self::FINISHED_COUNT, $now);

            $game = $this->createGame($homeTeam, $awayTeam, $slot['stage'], $startedAt, $tournament, 'finished');
            $this->createGoalsForGame($game, $homeTeam, $awayTeam);
            $this->createPredictionsForGame($game, $homeTeam, $awayTeam, $users, $league);
        }

        // Mark tournament winner and top scorer on pivot tables
        $winnerTeam      = $teams[self::WINNER_TEAM_INDEX];
        $topScorerPlayer = $winnerTeam->players->values()->get(self::TOP_SCORER_PLAYER_LOCAL_INDEX);

        $tournament->teams()->updateExistingPivot($winnerTeam->id, ['is_winner' => true]);
        $tournament->players()->updateExistingPivot($topScorerPlayer->id, ['is_top_scorer' => true]);

        // Base rank rows
        $this->createRanks($users, $league, self::FINISHED_COUNT);

        // Apply bonuses for users whose champion pick matched
        $users->each(function (User $user) use ($league, $winnerTeam, $topScorerPlayer): void {
            $champion = Champion::where('user_id', $user->id)->first();
            if (! $champion instanceof Champion) {
                return;
            }

            $isWinner    = $champion->team_id === $winnerTeam->id;
            $isTopScorer = $champion->player_id === $topScorerPlayer->id;

            if (! $isWinner && ! $isTopScorer) {
                return;
            }

            $bonus = ($isWinner ? 15 : 0) + ($isTopScorer ? 10 : 0);

            DB::table('ranks')
                ->where('user_id', $user->id)
                ->where('league_id', $league->id)
                ->update([
                    'winner'     => $isWinner,
                    'top_scorer' => $isTopScorer,
                    'total'      => DB::raw("total + {$bonus}"),
                ]);
        });
    }
}
```

- [ ] **Step 2: Run and spot-check**

```bash
php artisan migrate:fresh && php artisan db:seed --class=DevFinalSeeder
```

```bash
php artisan tinker --execute="
use App\Models\Game;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
echo 'Finished: '     . Game::where('status','finished')->count() . PHP_EOL;
echo 'Predictions: '  . Prediction::count() . PHP_EOL;
echo 'Rank rows: '    . DB::table('ranks')->count() . PHP_EOL;
echo 'Winners: '      . DB::table('ranks')->where('winner', true)->count() . PHP_EOL;
echo 'Top scorers: '  . DB::table('ranks')->where('top_scorer', true)->count() . PHP_EOL;
echo 'Spain winner: ' . DB::table('team_tournament')->where('is_winner', true)->count() . ' team(s)' . PHP_EOL;
"
```
Expected:
```
Finished: 52
Predictions: 780   (52 × 15)
Rank rows: 15
Winners: ≥0        (random champion picks; may be 0 if none picked Spain)
Top scorers: ≥0
Spain winner: 1 team(s)
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/DevFinalSeeder.php
git commit -m "feat: add DevFinalSeeder - phase D, full tournament complete with winner and top-scorer bonuses"
```

---

## Quick Reference

```bash
# Phase A — before the tournament starts
php artisan migrate:fresh && php artisan db:seed --class=DevBeforeStartSeeder

# Phase B — mid group stage (matchday 1 + 2 done)
php artisan migrate:fresh && php artisan db:seed --class=DevGroupPhaseSeeder

# Phase C — all groups done + partial R16
php artisan migrate:fresh && php artisan db:seed --class=DevKnockoutPhaseSeeder

# Phase D — final done, full rankings
php artisan migrate:fresh && php artisan db:seed --class=DevFinalSeeder
```

Login as any named user with password `password`.