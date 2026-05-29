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
    protected const array TEAMS = [
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
    protected const array PLAYERS = [
        0 => [['first_name' => 'Manuel',    'last_name' => 'Neuer',            'displayed_name' => 'M. Neuer'],
            ['first_name' => 'Thomas',    'last_name' => 'Müller',           'displayed_name' => 'T. Müller'],
            ['first_name' => 'Joshua',    'last_name' => 'Kimmich',          'displayed_name' => 'J. Kimmich']],
        1 => [['first_name' => 'Andrew',    'last_name' => 'Robertson',        'displayed_name' => 'A. Robertson'],
            ['first_name' => 'Scott',     'last_name' => 'McTominay',        'displayed_name' => 'S. McTominay'],
            ['first_name' => 'Lyndon',    'last_name' => 'Dykes',            'displayed_name' => 'L. Dykes']],
        2 => [['first_name' => 'Dominik',   'last_name' => 'Szoboszlai',       'displayed_name' => 'D. Szoboszlai'],
            ['first_name' => 'Roland',    'last_name' => 'Varga',            'displayed_name' => 'R. Varga'],
            ['first_name' => 'Roland',    'last_name' => 'Sallai',           'displayed_name' => 'R. Sallai']],
        3 => [['first_name' => 'Xherdan',   'last_name' => 'Shaqiri',          'displayed_name' => 'X. Shaqiri'],
            ['first_name' => 'Manuel',    'last_name' => 'Akanji',           'displayed_name' => 'M. Akanji'],
            ['first_name' => 'Breel',     'last_name' => 'Embolo',           'displayed_name' => 'B. Embolo']],
        4 => [['first_name' => 'Lamine',    'last_name' => 'Yamal',            'displayed_name' => 'L. Yamal'],
            ['first_name' => 'Alvaro',    'last_name' => 'Morata',           'displayed_name' => 'A. Morata'],
            ['first_name' => 'Pedro',     'last_name' => 'González',         'displayed_name' => 'Pedri']],
        5 => [['first_name' => 'Luka',      'last_name' => 'Modric',           'displayed_name' => 'L. Modric'],
            ['first_name' => 'Josko',     'last_name' => 'Gvardiol',         'displayed_name' => 'J. Gvardiol'],
            ['first_name' => 'Andrej',    'last_name' => 'Budimir',          'displayed_name' => 'A. Budimir']],
        6 => [['first_name' => 'Gianluigi', 'last_name' => 'Donnarumma',       'displayed_name' => 'G. Donnarumma'],
            ['first_name' => 'Nicolo',    'last_name' => 'Barella',          'displayed_name' => 'N. Barella'],
            ['first_name' => 'Mateo',     'last_name' => 'Retegui',          'displayed_name' => 'M. Retegui']],
        7 => [['first_name' => 'Elseid',    'last_name' => 'Bajrami',          'displayed_name' => 'E. Bajrami'],
            ['first_name' => 'Kristjan',  'last_name' => 'Asllani',          'displayed_name' => 'K. Asllani'],
            ['first_name' => 'Armando',   'last_name' => 'Broja',            'displayed_name' => 'A. Broja']],
        8 => [['first_name' => 'Harry',     'last_name' => 'Kane',             'displayed_name' => 'H. Kane'],
            ['first_name' => 'Bukayo',    'last_name' => 'Saka',             'displayed_name' => 'B. Saka'],
            ['first_name' => 'Jude',      'last_name' => 'Bellingham',       'displayed_name' => 'J. Bellingham']],
        9 => [['first_name' => 'Dusan',     'last_name' => 'Vlahovic',         'displayed_name' => 'D. Vlahovic'],
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
        ['stage' => 'Round of 16',  'home' => 4,  'away' => 5],
        ['stage' => 'Round of 16',  'home' => 0,  'away' => 10],
        ['stage' => 'Round of 16',  'home' => 12, 'away' => 16],
        ['stage' => 'Round of 16',  'home' => 22, 'away' => 11],
        ['stage' => 'Round of 16',  'home' => 8,  'away' => 17],
        ['stage' => 'Round of 16',  'home' => 3,  'away' => 6],
        ['stage' => 'Round of 16',  'home' => 14, 'away' => 18],
        ['stage' => 'Round of 16',  'home' => 20, 'away' => 13],
        // Quarter-Finals (indices 44-47)
        ['stage' => 'Quarter-Final', 'home' => 4,  'away' => 0],
        ['stage' => 'Quarter-Final', 'home' => 12, 'away' => 22],
        ['stage' => 'Quarter-Final', 'home' => 8,  'away' => 3],
        ['stage' => 'Quarter-Final', 'home' => 14, 'away' => 20],
        // Semi-Finals (indices 48-49)
        ['stage' => 'Semi-Final',   'home' => 4,  'away' => 12],
        ['stage' => 'Semi-Final',   'home' => 8,  'away' => 14],
        // Final 3/4 (index 50)
        ['stage' => 'Final 3/4',    'home' => 12, 'away' => 14],
        // Final (index 51) — Spain wins
        ['stage' => 'Final',        'home' => 4,  'away' => 8],
    ];

    // Spain (TEAMS index 4) wins; Yamal (PLAYERS[4][0]) is top scorer
    protected const int WINNER_TEAM_INDEX = 4;

    protected const int TOP_SCORER_TEAM_INDEX = 4;

    protected const int TOP_SCORER_PLAYER_LOCAL_INDEX = 0;

    protected function computeStartedAt(int $gameIndex, int $finishedCount, Carbon $now): Carbon
    {
        if ($gameIndex < $finishedCount) {
            $daysAgo = (int) (($finishedCount - 1 - $gameIndex) / 2) + 1;
            $hour = 18 + ($gameIndex % 2) * 3;

            return $now->copy()->subDays($daysAgo)->setHour($hour)->setMinute(0)->setSecond(0);
        }

        $futureIndex = $gameIndex - $finishedCount;
        $daysFromNow = (int) ($futureIndex / 2) + 1;
        $hour = 18 + ($futureIndex % 2) * 3;

        return $now->copy()->addDays($daysFromNow)->setHour($hour)->setMinute(0)->setSecond(0);
    }

    protected function createTournament(Carbon $start, Carbon $finalStart): Tournament
    {
        return Tournament::create([
            'country' => 'World',
            'name' => 'UEFA Euro Cup',
            'logo' => 'https://media.api-sports.io/football/leagues/4.png',
            'is_cup' => true,
            'season' => 2024,
            'api_id' => 4,
            'started_at' => $start,
            'final_started_at' => $finalStart,
        ]);
    }

    protected function createLeague(Tournament $tournament): League
    {
        return $tournament->leagues()->create(['name' => 'Fantapronostico2024']);
    }

    protected function createTeamsAndPlayers(Tournament $tournament): Collection
    {
        $teams = collect();

        foreach (self::TEAMS as $idx => $teamData) {
            $team = Team::create([
                'name' => $teamData['name'],
                'code' => $teamData['code'],
                'api_id' => $teamData['api_id'],
                'logo' => $teamData['logo'],
                'is_national' => true,
            ]);

            $tournament->teams()->attach($team->id, ['is_winner' => false]);

            foreach (self::PLAYERS[$idx] as $playerData) {
                $player = Player::create([
                    'displayed_name' => $playerData['displayed_name'],
                    'first_name' => $playerData['first_name'],
                    'last_name' => $playerData['last_name'],
                    'national_id' => $team->id,
                ]);
                $tournament->players()->attach($player->id, ['is_top_scorer' => false]);
            }

            $team->setRelation('players', Player::where('national_id', $team->id)->get());
            $teams->push($team);
        }

        return $teams;
    }

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
        $allUsers = $namedUsers->merge($fakerUsers);

        $allUsers->each(function (User $user) use ($league): void {
            $user->update(['selected_league_id' => $league->id]);
            $league->users()->attach($user->id, ['status' => 'accepted']);
        });

        Role::create(['user_id' => $namedUsers[0]->id, 'role' => RoleEnum::ADMIN, 'league_id' => null]);
        Role::create(['user_id' => $namedUsers[1]->id, 'role' => RoleEnum::MOD,   'league_id' => $league->id]);

        return $allUsers;
    }

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
            'stage' => $stage,
            'status' => $status,
            'started_at' => $startedAt,
        ]);

        $game->teams()->attach($homeTeam->id, ['is_away' => false]);
        $game->teams()->attach($awayTeam->id, ['is_away' => true]);

        $homeTeam->players->each(fn (Player $p) => $game->players()->attach($p->id));
        $awayTeam->players->each(fn (Player $p) => $game->players()->attach($p->id));

        return $game;
    }

    protected function createGoalsForGame(Game $game, Team $homeTeam, Team $awayTeam): void
    {
        $allPlayers = $homeTeam->players->merge($awayTeam->players);
        $goalCount = random_int(1, 3);

        for ($i = 0; $i < $goalCount; $i++) {
            GameGoal::create([
                'game_id' => $game->id,
                'player_id' => $allPlayers->random()->id,
                'is_autogoal' => false,
                'scored_at' => $game->started_at->copy()->addMinutes(random_int(10, 85)),
            ]);
        }
    }

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
            $sign = match (true) {
                $homeScore > $awayScore => '1',
                $homeScore < $awayScore => '2',
                default => 'x',
            };

            Prediction::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'league_id' => $league->id,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'sign' => $sign,
                'home_scorer_id' => $homeScore > 0 ? $homeTeam->players->random()->id : 0,
                'away_scorer_id' => $awayScore > 0 ? $awayTeam->players->random()->id : 0,
            ]);
        });
    }

    protected function createChampions(Collection $users, Collection $teams, array $skipEmails = []): void
    {
        $now = now();

        $users->each(function (User $user) use ($teams, $skipEmails, $now): void {
            if (in_array($user->email, $skipEmails, true)) {
                return;
            }
            $team = $teams->random();
            Champion::create([
                'user_id' => $user->id,
                'team_id' => $team->id,
                'player_id' => $team->players->random()->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    protected function createRanks(Collection $users, League $league, int $finishedGamesCount): void
    {
        $users->each(function (User $user) use ($league, $finishedGamesCount): void {
            $signs = random_int(0, $finishedGamesCount);
            $results = random_int(0, (int) ($signs * 0.4));
            $scorers = random_int(0, $finishedGamesCount * 2);
            $total = $signs + $results * 2 + $scorers * 3;

            DB::table('ranks')->insert([
                'user_id' => $user->id,
                'league_id' => $league->id,
                'total' => $total,
                'results' => $results,
                'scorers' => $scorers,
                'signs' => $signs,
                'final_total' => 0,
                'final_timestamp' => null,
                'winner' => false,
                'top_scorer' => false,
                'from' => null,
            ]);
        });
    }
}
