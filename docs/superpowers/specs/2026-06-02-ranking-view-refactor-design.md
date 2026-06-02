# Ranking View Refactor Design

## Problem

The current `ranks` table is a mutable accumulator. Points are added incrementally on each `calculate()` run, guarded by a `from` timestamp to avoid double-counting predictions. Champion bonus (winner/top scorer) has no guard and double-counts on every run. This design makes the system stateful, fragile to re-runs, and has no per-prediction history.

## Goal

Replace the accumulator with a normalised scoring store:
- One row per prediction result (`prediction_rank`)
- One row per champion evaluation per user per league (`champion_rank`)
- A PostgreSQL materialized view (`ranking_view`) that aggregates both

Scoring becomes idempotent. History is preserved at the prediction level. Point values are stored alongside results for auditability.

---

## Two-Phase Delivery

### Phase 1 — Champion scoping (prerequisite)

Add `league_id` to the `champions` table and update the champion creation flow to set it. This is required for Phase 2's `scoreChampion` to correctly scope champion predictions per league.

- Migration: add `league_id` (FK leagues, NOT NULL) to `champions`
- Update champion creation flow to set `league_id`
- No changes to ranking logic yet

### Phase 2 — Parallel run (this spec)

New tables, view, class, and command alongside the existing system. No existing ranking code is modified. Both commands can be run against the same data and results compared. `scoreChampion` uses `league_id` from Phase 1.

### Phase 3 — Cutover (separate spec)

- Bind `ViewRankingCalculator` to `RankingCalculatorInterface`
- Drop `ranks` table
- Retire `CalculateRanking` command

---

## Scoring Values

Canonical values live in a new `ScoringValues` class. Stored alongside computed rows so history survives future rule changes.

| Constant | Value |
|---|---|
| `EXACT` | 4 |
| `SIGN` | 1 |
| `SCORER` | 3 (per scorer — home and away counted separately) |
| `WINNER` | 15 |
| `TOP_SCORER` | 15 |

---

## Schema — Phase 2

### `prediction_rank`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | bigint FK users | |
| `prediction_id` | bigint FK predictions | |
| `league_id` | bigint FK leagues | |
| `game_id` | bigint FK games | |
| `is_exact` | boolean NOT NULL | |
| `is_sign` | boolean NOT NULL | |
| `is_home_scorer` | boolean NOT NULL | |
| `is_away_scorer` | boolean NOT NULL | |
| `value_exact` | smallint NOT NULL | stored at computation time |
| `value_sign` | smallint NOT NULL | stored at computation time |
| `value_scorer` | smallint NOT NULL | stored at computation time |
| `total` | int NOT NULL | `is_exact*value_exact + is_sign*value_sign + (is_home_scorer+is_away_scorer)*value_scorer` |
| `is_final` | boolean NOT NULL | true if game is the tournament final |
| `predicted_at` | timestamp NOT NULL | copied from `predictions.updated_at` — used as final tiebreaker |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Unique constraint**: `(user_id, prediction_id)` — makes upsert idempotent.

### `champion_rank`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | bigint FK users | |
| `league_id` | bigint FK leagues | |
| `winner` | boolean NOT NULL | whether champion team prediction was correct |
| `top_scorer` | boolean NOT NULL | whether champion player prediction was correct |
| `value_winner` | smallint NOT NULL | stored at computation time |
| `value_top_scorer` | smallint NOT NULL | stored at computation time |
| `total` | int NOT NULL | `(winner ? value_winner : 0) + (top_scorer ? value_top_scorer : 0)` |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Unique constraint**: `(user_id, league_id)`.

Row presence is the state — absence means not yet computed, presence means evaluated (winner/top_scorer may be true or false).

### `ranking_view` (materialized view)

Anchored on `league_user` so users with zero predictions appear in the ranking. `cr.total` is `NOT NULL` in `champion_rank`, so referenced directly — `COALESCE` only needed for the LEFT JOIN case where no champion row exists yet.

```sql
CREATE MATERIALIZED VIEW ranking_view AS
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
LEFT JOIN prediction_rank pr ON pr.user_id = lu.user_id AND pr.league_id = lu.league_id
LEFT JOIN champion_rank cr   ON cr.user_id = lu.user_id AND cr.league_id = lu.league_id
WHERE lu.status = 'accepted'
GROUP BY
    lu.user_id, lu.league_id,
    cr.total, cr.winner, cr.top_scorer, cr.value_winner, cr.value_top_scorer;
```

---

## PHP Layer — Phase 2

### `ScoringValues` (new)

`App\Helpers\Ranking\ScoringValues` — final class, public constants only.

```
EXACT = 4
SIGN = 1
SCORER = 3
WINNER = 15
TOP_SCORER = 15
```

### `ViewRankingCalculator` (new)

`App\Helpers\Ranking\ViewRankingCalculator` — standalone, does NOT implement `RankingCalculatorInterface` until Phase 3.

**`calculate(League $league): void`**
1. Load accepted users for the league
2. For each user: call `scorePredictions()` then `scoreChampion()`
3. After all users: `DB::statement('REFRESH MATERIALIZED VIEW ranking_view')`

**`scorePredictions(User $user, League $league): void`** (private)
- Load all predictions for `$league->id` where game status is finished
- For each prediction: run `PredictionScoreFactory::create()`, upsert into `prediction_rank`
- Upsert key: `(user_id, prediction_id)` — safe to re-run

**`scoreChampion(User $user, League $league): void`** (private)
- Guard: `$league->tournament->final_started_at` must be set and in the past
- Guard: row already exists in `champion_rank` for `(user_id, league_id)` → return early
- Look up `Champion` by `user_id` and `league_id` (available from Phase 1)
- If no champion prediction: insert row with `winner=false`, `top_scorer=false`, `total=0`
- Otherwise: check winner via `team_tournaments.is_winner`, top scorer via `player_tournaments.is_top_scorer`
- INSERT (not upsert) — champion scoring is intentionally one-shot per user per league

### `CalculateRankingView` command (new)

`App\Console\Commands\CalculateRankingView`

Signature: `fp:ranking:calculate-view {--leagueId=}`

Same league resolution logic as existing `CalculateRanking`. Calls `ViewRankingCalculator::calculate()`. Logs the same output format for easy diff.

---

## Untouched in Phase 2

- `ranks` table
- `RankingCalculator`, `RankingCalculatorInterface`
- `CalculateRanking` command
- `PredictionScore`, `PredictionScoreFactory`
- `UserRank`, `Sorter`
- `AppServiceProvider` bindings

---

## Validation

After running both commands against the same data:

```sql
SELECT
    r.user_id,
    r.total      AS old_total,
    rv.total     AS new_total,
    r.total - rv.total AS diff
FROM ranks r
JOIN ranking_view rv USING (user_id, league_id)
WHERE r.total != rv.total
ORDER BY ABS(r.total - rv.total) DESC;
```

Expected differences: winner/top scorer points only (existing calculator has the double-count bug; new one does not). All other totals should match exactly.

---

## Migration List

### Phase 1
1. Add `league_id` to `champions` (FK leagues, NOT NULL — requires backfill if existing rows)

### Phase 2
2. Create `prediction_rank` table
3. Create `champion_rank` table
4. Create `ranking_view` materialized view