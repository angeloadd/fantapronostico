<?php

declare(strict_types=1);

namespace App\Helpers\Ranking;

use Closure;
use Illuminate\Support\Collection;

final class Sorter implements SorterInterface
{
    /**
     * @var array<int|string, string|Closure>
     */
    private array $sorters;

    public function __construct(Closure|string ...$sorters)
    {
        $this->sorters = $sorters;
    }

    /**
     * @param  Collection<int, UserRank>  $collection
     * @return Collection<int, UserRank>
     */
    public function sortAggregate(Collection $collection): Collection
    {
        /** @var Collection<int, UserRank> $collection */
        $collection = $this->sortCollectionByCallback($collection)
            ->flatten()
            ->values();

        return $collection;
    }

    /**
     * @param  Collection<int, UserRank>  $collection
     * @return Collection<int, UserRank>
     */
    private function sortCollectionByCallback(Collection $collection, int $index = 0): Collection
    {
        $currentSorter = $this->sorters[$index];

        if ($index === (count($this->sorters) - 1)) {
            return $collection->sortBy($currentSorter);
        }

        // @phpstan-ignore-next-line return.type
        return $collection->sortByDesc($currentSorter)
            ->groupBy($currentSorter)
            ->map(fn (Collection $collection) => $this->sortCollectionByCallback($collection, $index + 1));
    }
}
