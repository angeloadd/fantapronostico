<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class DumpProductionDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fp:dump:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tables = [
            'tournaments',
            'leagues',
            'users',
            'league_user',
            'teams',
            'games',
            'game_team',
            'players',
            'roles',
            'team_tournament',
            'game_goals',
            'champions',
            'predictions',
            'prediction_rank',
            'champion_rank',
        ];
        foreach ($tables as $table) {
            $tableEntries = DB::connection('pgsql_dump')->table($table)->get();
            foreach ($tableEntries as $tableEntry) {
                DB::connection('pgsql')->table($table)->insert((array) $tableEntry);
            }
        }

        return 0;
    }
}
