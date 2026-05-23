<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Ranking\RankingCalculatorInterface;
use App\Modules\League\Models\League;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Throwable;

final class CalculateRanking extends Command
{
    public const RANKING_OUTPUT = 'Updated rankings for league %s[id=%d]';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fp:ranking:calculate {--leagueId=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(LoggerInterface $logger, RankingCalculatorInterface $rankingCalculator): int
    {
        if (!is_numeric($this->option('leagueId'))) {
            $league = League::first();
        } else {
            $league = League::find($this->option('leagueId'));
        }

        if (null === $league) {
            $this->error('No league with id '.$this->option('leagueId').' found');
            $logger->error('No league with id '.$this->option('leagueId').' found');

            return self::FAILURE;
        }

        try {
            $rankingCalculator->calculate($league);
        } catch (Throwable $e) {
            $logger->error('Error updating rankings for league '.$league->name.'[id='.$league->id.']: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('Error updating rankings: '.$e->getMessage());

            return self::FAILURE;
        }

        $logger->info(sprintf(self::RANKING_OUTPUT, $league->name, $league->id));
        $this->info(sprintf(self::RANKING_OUTPUT, $league->name, $league->id));

        return self::SUCCESS;
    }
}
