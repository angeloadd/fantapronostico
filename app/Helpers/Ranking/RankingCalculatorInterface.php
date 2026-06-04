<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use App\Modules\League\Models\League;
use Illuminate\Support\Collection;

interface RankingCalculatorInterface
{
    public function calculate(League $league, bool $refresh = false): void;

    /**
     * @return Collection<int, UserRank>
     */
    public function get(League $league): Collection;
}
