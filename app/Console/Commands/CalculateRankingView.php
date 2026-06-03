<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Ranking\ViewRankingCalculator;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CalculateRankingView extends Command
{
    protected $signature = 'fp:ranking:calculate-view {--leagueId=} {--refresh}';

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
            if ($this->option('refresh') ?? false) {
                DB::statement('REFRESH MATERIALIZED VIEW ranking_view');
            } else {
                $calculator->calculate($league);
            }
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
