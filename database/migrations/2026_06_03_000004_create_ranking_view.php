<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW ranking_view AS
            WITH final_bets AS (
                SELECT DISTINCT ON (pr.user_id, pr.league_id)
                    pr.user_id,
                    pr.league_id,
                    pr.total        AS final_total,
                    pr.predicted_at AS final_timestamp
                FROM prediction_rank pr
                JOIN games g ON g.id = pr.game_id
                WHERE pr.is_final = true
                ORDER BY pr.user_id, pr.league_id, g.started_at DESC, pr.id DESC
            ),
            scores AS (
                SELECT
                    lu.user_id,
                    lu.league_id,
                    COALESCE(SUM(pr.total), 0) + COALESCE(cr.total, 0)               AS total,
                    COALESCE(SUM(pr.is_exact::int), 0)                                AS results,
                    COALESCE(SUM(pr.is_sign::int), 0)                                 AS signs,
                    COALESCE(SUM(pr.is_home_scorer::int + pr.is_away_scorer::int), 0) AS scorers,
                    COALESCE(fb.final_total, 0)                                        AS final_total,
                    fb.final_timestamp                                                  AS final_timestamp,
                    COALESCE(cr.winner, false)                                         AS winner,
                    COALESCE(cr.top_scorer, false)                                     AS top_scorer
                FROM league_user lu
                LEFT JOIN prediction_rank pr
                    ON pr.user_id = lu.user_id AND pr.league_id = lu.league_id
                LEFT JOIN champion_rank cr
                    ON cr.user_id = lu.user_id AND cr.league_id = lu.league_id
                LEFT JOIN final_bets fb
                    ON fb.user_id = lu.user_id AND fb.league_id = lu.league_id
                WHERE lu.status = 'accepted'
                GROUP BY lu.user_id, lu.league_id, cr.total, cr.winner, cr.top_scorer, fb.final_total, fb.final_timestamp
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
