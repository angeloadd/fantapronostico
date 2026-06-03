# Ranking View Refactor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add `league_id` to champion predictions (Phase 1 prerequisite), then create `prediction_rank`/`champion_rank` tables and a `ranking_view` materialized view with a parallel `ViewRankingCalculator` and `fp:ranking:calculate-view` command (Phase 2), running alongside the existing system for comparison.

**Architecture:** Phase 1 adds `league_id` to `champions`, changes the unique constraint from `(user_id)` to `(user_id, league_id)`, and threads the league through the creation flow. Phase 2 creates new tables, the materialized view, a standalone `ViewRankingCalculator`, and a new command — all additions, nothing removed from the existing system. Scoring values (4/1/3/15/15) are stored in each row for historical auditability.

**Tech Stack:** PHP 8.5, Laravel 13, PostgreSQL 17, Eloquent ORM

---

## Parallel Execution Guide

```
[Task 1] Phase 1: champions.league_id  (sequential prerequisite)
         ↓
[Tasks 2, 3, 4] Run in parallel (all independent of each other)
  [Task 2] ScoringValues class
  [Task 3] prediction_rank migration
  [Task 4] champion_rank migration
         ↓
[Task 5] ranking_view migration      ← depends on Tasks 3+4
[Task 6] ViewRankingCalculator       ← depends on Task 2
         ↓
[Task 7] CalculateRankingView command ← depends on Task 6
         ↓
[Task 8] Feature tests               ← depends on Tasks 5+6+7
```

---

## File Map
The app is not in prod so you can directly modify the champion migration
**Phase 1:**
- Modify: `database/migrations/2023_12_11_105514_create_champions_table.php`
- Modify: `app/Models/Champion.php`
- Modify: `app/Http/Controllers/ChampionController.php`
- Modify: `database/seeders/DevBaseSeeder.php`

**Phase 2:**
- Create: `app/Helpers/Ranking/ScoringValues.php`
- Create: `database/migrations/2026_06_03_000002_create_prediction_rank_table.php`
- Create: `database/migrations/2026_06_03_000003_create_champion_rank_table.php`
- Create: `database/migrations/2026_06_03_000004_create_ranking_view.php`
- Create: `app/Helpers/Ranking/ViewRankingCalculator.php`
- Create: `app/Console/Commands/CalculateRankingView.php`
- Create: `tests/Feature/Helpers/Ranking/ViewRankingCalculatorTest.php`

---

## Task 1 (Phase 1): Add league_id to champions

**Files:**
- Modify: `database/migrations/2023_12_11_105514_create_champions_table.php`
- Modify: `app/Models/Champion.php`
- Modify: `app/Http/Controllers/ChampionController.php`
- Modify: `database/seeders/DevBaseSeeder.php`

- [ ] **Step 1: Modify existing champions migration**

Replace the full contents of `database/migrations/2023_12_11_105514_create_champions_table.php`:

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    private const TABLE_NAME = 'champions';

    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('player_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('league_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->timestamps(6);
                $table->unique(['user_id', 'league_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
```

- [ ] **Step 2: Update Champion model**

In `app/Models/Champion.php`, add `'league_id'` to `$fillable` and add the `league()` relation. Add imports `use App\Modules\League\Models\League;` and `use Illuminate\Database\Eloquent\Relations\BelongsTo;`.

```php
protected $fillable = [
    'user_id',
    'team_id',
    'player_id',
    'league_id',
];

/**
 * @return BelongsTo<League, $this>
 */
public function league(): BelongsTo
{
    return $this->belongsTo(League::class);
}
```

- [ ] **Step 3: Update ChampionController::store()**

Replace the `store()` method in `app/Http/Controllers/ChampionController.php`:

```php
public function store(ChampionRequest $request): RedirectResponse
{
    if ($this->competitionStarted()) {
        return redirect(route('champion.index'))
            ->with(
                'error_message',
                'La competizione è già iniziata, non puoi più inserire o modificare un pronostico'
            );
    }

    $leagueId = Auth::user()->selected_league_id;

    $champion = Champion::where('user_id', Auth::user()->id)
        ->where('league_id', $leagueId)
        ->first();

    if ($champion) {
        return redirect(route('champion.show', compact('champion')));
    }

    $champion = Auth::user()?->champion()->create([
        'team_id'   => $request->input('winner'),
        'player_id' => $request->input('topScorer'),
        'league_id' => $leagueId,
    ]);

    return redirect(route('champion.show', compact('champion')))
        ->with('message', 'Pronostico inserito con successo');
}
```

- [ ] **Step 4: Update DevBaseSeeder::createChampions()**

In `database/seeders/DevBaseSeeder.php`, update the signature to accept a `League` and include `league_id` in the create call. Add `use App\Modules\League\Models\League;` if not present.

```php
protected function createChampions(Collection $users, Collection $teams, League $league, array $skipEmails = []): void
{
    $now = now();

    $users->each(function (User $user) use ($teams, $league, $skipEmails, $now): void {
        if (in_array($user->email, $skipEmails, true)) {
            return;
        }
        $team = $teams->random();
        Champion::create([
            'user_id'    => $user->id,
            'team_id'    => $team->id,
            'player_id'  => $team->players->random()->id,
            'league_id'  => $league->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    });
}
```

Find every call to `$this->createChampions(...)` in `DevBaseSeeder.php` and add the `$league` argument as the third parameter.

- [ ] **Step 5: Apply and commit**

```bash
php artisan migrate:fresh --seed
git add database/migrations/2023_12_11_105514_create_champions_table.php \
        app/Models/Champion.php \
        app/Http/Controllers/ChampionController.php \
        database/seeders/DevBaseSeeder.php
git commit -m "feat: add league_id to champions table and creation flow"
```

---

## Task 2 (Phase 2): ScoringValues class

**Can run in parallel with Tasks 3 and 4.**

- [ ] **Step 1: Create file**

```php
<?php
// app/Helpers/Ranking/ScoringValues.php

declare(strict_types=1);

namespace App\Helpers\Ranking;

final class ScoringValues
{
    public const int EXACT = 4;
    public const int SIGN = 1;
    public const int SCORER = 3;
    public const int WINNER = 15;
    public const int TOP_SCORER = 15;
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Helpers/Ranking/ScoringValues.php
git commit -m "feat: add ScoringValues constants class"
```

---

## Task 3 (Phase 2): prediction_rank migration

**Can run in parallel with Tasks 2 and 4.**

- [ ] **Step 1: Create migration**

```php
<?php
// database/migrations/2026_06_03_000002_create_prediction_rank_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    private const TABLE_NAME = 'prediction_rank';

    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('prediction_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('league_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('game_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->boolean('is_exact');
                $table->boolean('is_sign');
                $table->boolean('is_home_scorer');
                $table->boolean('is_away_scorer');
                $table->smallInteger('value_exact');
                $table->smallInteger('value_sign');
                $table->smallInteger('value_scorer');
                $table->integer('total');
                $table->boolean('is_final');
                $table->timestamp('predicted_at');
                $table->timestamps();
                $table->unique(['user_id', 'prediction_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
```

- [ ] **Step 2: Run migration**
avoid running the migration,
```bash
php artisan migrate
```

Expected: `Migrating: 2026_06_03_000002_create_prediction_rank_table ... Done`

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_06_03_000002_create_prediction_rank_table.php
git commit -m "feat: create prediction_rank table"
```

---

## Task 4 (Phase 2): champion_rank migration

**Can run in parallel with Tasks 2 and 3.**

- [ ] **Step 1: Create migration**

```php
<?php
// database/migrations/2026_06_03_000003_create_champion_rank_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    private const TABLE_NAME = 'champion_rank';

    public function up(): void
    {
        Schema::create(
            self::TABLE_NAME,
            static function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('league_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->boolean('winner');
                $table->boolean('top_scorer');
                $table->smallInteger('value_winner');
                $table->smallInteger('value_top_scorer');
                $table->integer('total');
                $table->timestamps();
                $table->unique(['user_id', 'league_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
```

- [ ] **Step 2: Run migration**
avoid running the migration
```bash
php artisan migrate
```

Expected: `Migrating: 2026_06_03_000003_create_champion_rank_table ... Done`

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_06_03_000003_create_champion_rank_table.php
git commit -m "feat: create champion_rank table"
```

---

## Task 5 (Phase 2): ranking_view materialized view

**Depends on Tasks 3 and 4.**

- [ ] **Step 1: Create migration**

```php
<?php
// database/migrations/2026_06_03_000004_create_ranking_view.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW ranking_view AS
            WITH scores AS (
                SELECT
                    lu.user_id,
                    lu.league_id,
                    COALESCE(SUM(pr.total), 0) + COALESCE(cr.total, 0)               AS total,
                    COALESCE(SUM(pr.is_exact::int), 0)                                AS results,
                    COALESCE(SUM(pr.is_sign::int), 0)                                 AS signs,
                    COALESCE(SUM(pr.is_home_scorer::int + pr.is_away_scorer::int), 0) AS scorers,
                    COALESCE(MAX(CASE WHEN pr.is_final THEN pr.total END), 0)         AS final_total,
                    MIN(CASE WHEN pr.is_final THEN pr.predicted_at END)               AS final_timestamp,
                    COALESCE(cr.winner, false)                                        AS winner,
                    COALESCE(cr.top_scorer, false)                                    AS top_scorer
                FROM league_user lu
                LEFT JOIN prediction_rank pr
                    ON pr.user_id = lu.user_id AND pr.league_id = lu.league_id
                LEFT JOIN champion_rank cr
                    ON cr.user_id = lu.user_id AND cr.league_id = lu.league_id
                WHERE lu.status = 'accepted'
                GROUP BY lu.user_id, lu.league_id, cr.total, cr.winner, cr.top_scorer
            )
            SELECT
                ROW_NUMBER() OVER (
                    PARTITION BY s.league_id
                    ORDER BY
                        s.total           DESC,
                        s.results         DESC,
                        s.scorers         DESC,
                        s.signs           DESC,
                        s.final_total     DESC,
                        s.final_timestamp ASC NULLS LAST,
                        u.name            ASC
                )             AS position,
                s.user_id,
                s.league_id,
                u.name        AS user_name,
                s.total,
                s.results,
                s.signs,
                s.scorers,
                s.final_total,
                s.final_timestamp,
                s.winner,
                s.top_scorer
            FROM scores s
            JOIN users u ON u.id = s.user_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS ranking_view');
    }
};
```

- [ ] **Step 2: Run migration**
avoid running the migration
```bash
php artisan migrate
```

Expected: `Migrating: 2026_06_03_000004_create_ranking_view ... Done`

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_06_03_000004_create_ranking_view.php
git commit -m "feat: create ranking_view materialized view"
```

---

## Task 6 (Phase 2): ViewRankingCalculator

**Depends on Task 2 (ScoringValues).**

- [ ] **Step 1: Create class**

```php
<?php
// app/Helpers/Ranking/ViewRankingCalculator.php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use App\Enums\GameStatus;
use App\Models\Champion;
use App\Models\Prediction;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Illuminate\Support\Facades\DB;

final readonly class ViewRankingCalculator
{
    public function calculate(League $league): void
    {
        $league->users
            ->filter(static fn (User $user) => 'accepted' === $user->pivot->status)
            ->each(function (User $user) use ($league): void {
                $this->scorePredictions($user, $league);
                $this->scoreChampion($user, $league);
            });

        DB::statement('REFRESH MATERIALIZED VIEW ranking_view');
    }

    private function scorePredictions(User $user, League $league): void
    {
        $scoredIds = DB::table('prediction_rank')
            ->where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->pluck('prediction_id');

        $user->predictions
            ->whereStrict('league_id', $league->id)
            ->filter(fn (Prediction $prediction) => GameStatus::FINISHED === $prediction->game->status)
            ->reject(fn (Prediction $prediction) => $scoredIds->contains($prediction->id))
            ->each(function (Prediction $prediction) use ($user, $league): void {
                $score = PredictionScoreFactory::create($prediction);

                DB::table('prediction_rank')->insert([
                    'user_id'        => $user->id,
                    'prediction_id'  => $prediction->id,
                    'league_id'      => $league->id,
                    'game_id'        => $prediction->game_id,
                    'is_exact'       => $score->result,
                    'is_sign'        => $score->sign,
                    'is_home_scorer' => $score->homeScorer,
                    'is_away_scorer' => $score->awayScorer,
                    'value_exact'    => ScoringValues::EXACT,
                    'value_sign'     => ScoringValues::SIGN,
                    'value_scorer'   => ScoringValues::SCORER,
                    'total'          => ($score->result ? ScoringValues::EXACT : 0)
                                      + ($score->sign ? ScoringValues::SIGN : 0)
                                      + (($score->homeScorer ? 1 : 0) + ($score->awayScorer ? 1 : 0)) * ScoringValues::SCORER,
                    'is_final'       => $score->isFinal,
                    'predicted_at'   => $prediction->updated_at,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            });
    }

    private function scoreChampion(User $user, League $league): void
    {
        $tournament = $league->tournament;

        if (null === $tournament->final_started_at || $tournament->final_started_at->isFuture()) {
            return;
        }

        $alreadyScored = DB::table('champion_rank')
            ->where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->exists();

        if ($alreadyScored) {
            return;
        }

        $champion = Champion::where('user_id', $user->id)
            ->where('league_id', $league->id)
            ->first();

        $isWinner    = false;
        $isTopScorer = false;

        if ($champion instanceof Champion) {
            $winnerTeam   = $tournament->teams()->wherePivot('is_winner', true)->first();
            $topScorerIds = $tournament->players()->wherePivot('is_top_scorer', true)->pluck('players.id');
            $isWinner     = $winnerTeam instanceof Team && $winnerTeam->id === $champion->team_id;
            $isTopScorer  = $topScorerIds->contains($champion->player_id);
        }

        DB::table('champion_rank')->insert([
            'user_id'          => $user->id,
            'league_id'        => $league->id,
            'winner'           => $isWinner,
            'top_scorer'       => $isTopScorer,
            'value_winner'     => ScoringValues::WINNER,
            'value_top_scorer' => ScoringValues::TOP_SCORER,
            'total'            => ($isWinner ? ScoringValues::WINNER : 0)
                                + ($isTopScorer ? ScoringValues::TOP_SCORER : 0),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Helpers/Ranking/ViewRankingCalculator.php
git commit -m "feat: add ViewRankingCalculator"
```

---

## Task 7 (Phase 2): CalculateRankingView command

**Depends on Task 6.**

- [ ] **Step 1: Create command**

```php
<?php
// app/Console/Commands/CalculateRankingView.php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Ranking\ViewRankingCalculator;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Throwable;

final class CalculateRankingView extends Command
{
    protected $signature = 'fp:ranking:calculate-view {--leagueId=}';
    protected $description = 'Recalculate ranking_view (parallel run — compare with fp:ranking:calculate)';

    public function handle(ViewRankingCalculator $calculator): int
    {
        $league = is_numeric($this->option('leagueId'))
            ? League::find((int) $this->option('leagueId'))
            : League::first();

        if (!$league instanceof League) {
            $this->error('League not found');
            return self::FAILURE;
        }

        try {
            $calculator->calculate($league);
        } catch (Throwable $e) {
            $this->error('Error: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Updated ranking_view for league %s [id=%d]',
            $league->name,
            $league->id
        ));

        return self::SUCCESS;
    }
}
```

- [ ] **Step 2: Verify command is discoverable**

```bash
php artisan list | grep fp:ranking
```

Expected output:
```
fp:ranking:calculate
fp:ranking:calculate-view
```

- [ ] **Step 3: Commit**

```bash
git add app/Console/Commands/CalculateRankingView.php
git commit -m "feat: add fp:ranking:calculate-view Artisan command"
```

---

## Task 8 (Phase 2): Feature tests

**Depends on Tasks 5, 6, 7.**

Uses `DatabaseMigrations` (not `RefreshDatabase`) because `REFRESH MATERIALIZED VIEW` cannot execute inside a PostgreSQL transaction — which `RefreshDatabase` uses to wrap each test.

- [ ] **Step 1: Create test class**

```php
<?php
// tests/Feature/Helpers/Ranking/ViewRankingCalculatorTest.php

declare(strict_types=1);

namespace Tests\Feature\Helpers\Ranking;

use App\Enums\GameStatus;
use App\Helpers\Ranking\ScoringValues;
use App\Helpers\Ranking\ViewRankingCalculator;
use App\Models\Champion;
use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
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

        $this->calculator = new ViewRankingCalculator();
        $this->tournament = Tournament::factory()->create(['final_started_at' => null]);
        $this->league     = League::factory()->create(['tournament_id' => $this->tournament->id]);
        $this->user       = User::factory()->create();
        $this->league->users()->attach($this->user->id, ['status' => 'accepted']);
    }

    public function test_creates_prediction_rank_row_for_finished_game(): void
    {
        [$game] = $this->createFinishedGame(homeScore: 2, awayScore: 1);

        DB::table('predictions')->insert([
            'user_id'        => $this->user->id,
            'game_id'        => $game->id,
            'league_id'      => $this->league->id,
            'home_score'     => 2,
            'away_score'     => 1,
            'sign'           => '1',
            'home_scorer_id' => null,
            'away_scorer_id' => null,
            'created_at'     => now(),
            'updated_at'     => now(),
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
            'status'        => GameStatus::NOT_STARTED->value,
            'stage'         => 'Group Stage - 1',
            'started_at'    => now()->addDay(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('predictions')->insert([
            'user_id'    => $this->user->id,
            'game_id'    => $gameId,
            'league_id'  => $this->league->id,
            'home_score' => 1,
            'away_score' => 0,
            'sign'       => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->calculator->calculate($this->league);

        $this->assertSame(0, DB::table('prediction_rank')->count());
    }

    public function test_idempotent_on_second_run(): void
    {
        [$game] = $this->createFinishedGame(homeScore: 1, awayScore: 0);

        DB::table('predictions')->insert([
            'user_id'    => $this->user->id,
            'game_id'    => $game->id,
            'league_id'  => $this->league->id,
            'home_score' => 1,
            'away_score' => 0,
            'sign'       => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->calculator->calculate($this->league);
        $this->calculator->calculate($this->league);

        $this->assertSame(1, DB::table('prediction_rank')->count());
    }

    public function test_champion_rank_not_created_before_final(): void
    {
        $this->tournament->update(['final_started_at' => null]);

        $this->calculator->calculate($this->league);

        $this->assertSame(0, DB::table('champion_rank')->count());
    }

    public function test_champion_rank_created_with_correct_winner_and_top_scorer(): void
    {
        $winnerTeam  = Team::factory()->create();
        $scorerId    = $this->insertPlayer($winnerTeam);

        $this->tournament->update(['final_started_at' => Carbon::now()->subDay()]);
        $this->tournament->teams()->attach($winnerTeam->id, ['is_winner' => true]);
        $this->tournament->players()->attach($scorerId, ['is_top_scorer' => true]);

        Champion::create([
            'user_id'   => $this->user->id,
            'team_id'   => $winnerTeam->id,
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

    private function createFinishedGame(int $homeScore, int $awayScore): array
    {
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $gameId = DB::table('games')->insertGetId([
            'tournament_id' => $this->tournament->id,
            'status'        => GameStatus::FINISHED->value,
            'stage'         => 'Group Stage - 1',
            'started_at'    => now()->subDay(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('game_teams')->insert([
            ['game_id' => $gameId, 'team_id' => $homeTeam->id, 'is_away' => false, 'score' => $homeScore],
            ['game_id' => $gameId, 'team_id' => $awayTeam->id, 'is_away' => true,  'score' => $awayScore],
        ]);

        $game = \App\Models\Game::find($gameId);

        return [$game, $homeTeam, $awayTeam];
    }

    private function insertPlayer(Team $team): int
    {
        return DB::table('players')->insertGetId([
            'displayed_name' => 'Test Player',
            'first_name'     => 'Test',
            'last_name'      => 'Player',
            'national_id'    => $team->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add tests/Feature/Helpers/Ranking/ViewRankingCalculatorTest.php
git commit -m "test: feature tests for ViewRankingCalculator"
```
