<?php

declare(strict_types=1);

namespace Tests\Feature\Helpers\Ranking;

use App\Enums\GameStatus;
use App\Helpers\Ranking\ScoringValues;
use App\Helpers\Ranking\ViewRankingCalculator;
use App\Models\Champion;
use App\Models\Game;
use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

final class ViewRankingCalculatorTest extends TestCase
{
    use DatabaseMigrations;

    private ViewRankingCalculator $calculator;

    private League $league;

    private Tournament $tournament;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $logger = $this->createStub(LoggerInterface::class);
        $this->calculator = new ViewRankingCalculator($logger);
        $this->tournament = Tournament::factory()->create(['final_started_at' => Carbon::now()->addYear()]);
        $this->league = League::create([
            'tournament_id' => $this->tournament->id,
            'name' => 'test',
        ]);
        $this->user = User::factory()->create();
        $this->league->users()->attach($this->user->id, ['status' => 'accepted']);
    }

    public function test_creates_prediction_rank_row_for_finished_game(): void
    {
        // 0-0 game: no goals needed, sign='x', prediction matches exactly
        [$game] = $this->createFinishedGame();

        DB::table('predictions')->insert([
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'league_id' => $this->league->id,
            'home_score' => 0,
            'away_score' => 0,
            'sign' => 'x',
            'home_scorer_id' => null,
            'away_scorer_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->calculator->calculate($this->league);

        $this->assertSame(1, DB::table('prediction_rank')->count());

        $row = DB::table('prediction_rank')->first();
        $this->assertTrue((bool) $row->is_exact);
        $this->assertTrue((bool) $row->is_sign);
        $this->assertFalse((bool) $row->is_home_scorer);
        $this->assertFalse((bool) $row->is_away_scorer);
        $this->assertSame(ScoringValues::EXACT + ScoringValues::SIGN, $row->total);
        $this->assertSame(ScoringValues::EXACT, $row->value_exact);
        $this->assertSame(ScoringValues::SIGN, $row->value_sign);
        $this->assertSame(ScoringValues::SCORER, $row->value_scorer);
    }

    public function test_skips_not_finished_games(): void
    {
        $gameId = DB::table('games')->insertGetId([
            'tournament_id' => $this->tournament->id,
            'status' => GameStatus::NOT_STARTED->value,
            'stage' => 'Group Stage - 1',
            'started_at' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('predictions')->insert([
            'user_id' => $this->user->id,
            'game_id' => $gameId,
            'league_id' => $this->league->id,
            'home_score' => 1,
            'away_score' => 0,
            'sign' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->calculator->calculate($this->league);

        $this->assertSame(0, DB::table('prediction_rank')->count());
    }

    public function test_idempotent_on_second_run(): void
    {
        [$game] = $this->createFinishedGame();

        DB::table('predictions')->insert([
            'user_id' => $this->user->id,
            'game_id' => $game->id,
            'league_id' => $this->league->id,
            'home_score' => 0,
            'away_score' => 0,
            'sign' => 'x',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->calculator->calculate($this->league);
        $this->calculator->calculate($this->league);

        $this->assertSame(1, DB::table('prediction_rank')->count());
    }

    public function test_champion_rank_not_created_before_final(): void
    {
        // final_started_at is in the future (set in setUp via addYear()) — champion rank must not be created yet
        $this->calculator->calculate($this->league);

        $this->assertSame(0, DB::table('champion_rank')->count());
    }

    public function test_champion_rank_created_with_correct_winner_and_top_scorer(): void
    {
        $winnerTeam = Team::factory()->create();
        $scorerId = $this->insertPlayer($winnerTeam);

        $this->tournament->update(['final_started_at' => Carbon::now()->subDay()]);
        $this->tournament->teams()->attach($winnerTeam->id, ['is_winner' => true]);
        $this->tournament->players()->attach($scorerId, ['is_top_scorer' => true]);

        Champion::create([
            'user_id' => $this->user->id,
            'team_id' => $winnerTeam->id,
            'player_id' => $scorerId,
            'league_id' => $this->league->id,
        ]);

        $this->calculator->calculate($this->league);

        $row = DB::table('champion_rank')->where('user_id', $this->user->id)->first();

        $this->assertNotNull($row);
        $this->assertTrue((bool) $row->winner);
        $this->assertTrue((bool) $row->top_scorer);
        $this->assertSame(ScoringValues::WINNER + ScoringValues::TOP_SCORER, $row->total);
        $this->assertSame(ScoringValues::WINNER, $row->value_winner);
        $this->assertSame(ScoringValues::TOP_SCORER, $row->value_top_scorer);
    }

    public function test_champion_rank_not_duplicated_on_second_run(): void
    {
        $this->tournament->update(['final_started_at' => Carbon::now()->subDay()]);

        $this->calculator->calculate($this->league);
        $this->calculator->calculate($this->league);

        $this->assertSame(1, DB::table('champion_rank')->count());
    }

    // --- helpers ---

    /**
     * @return array{?Game, Team, Team}
     */
    private function createFinishedGame(): array
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $gameId = DB::table('games')->insertGetId([
            'tournament_id' => $this->tournament->id,
            'status' => GameStatus::FINISHED->value,
            'stage' => 'Group Stage - 1',
            'started_at' => now()->subDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('game_team')->insert([
            ['game_id' => $gameId, 'team_id' => $homeTeam->id, 'is_away' => false],
            ['game_id' => $gameId, 'team_id' => $awayTeam->id, 'is_away' => true],
        ]);

        $game = Game::find($gameId);

        return [$game, $homeTeam, $awayTeam];
    }

    private function insertPlayer(Team $team): int
    {
        return DB::table('players')->insertGetId([
            'displayed_name' => 'Test Player',
            'first_name' => 'Test',
            'last_name' => 'Player',
            'national_id' => $team->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
