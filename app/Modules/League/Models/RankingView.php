<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Read-only model backed by the ranking_view materialized view.
 * Use RankingView::forLeague($leagueId)->get() to read rankings.
 * Writes are not supported — the underlying view is refreshed via
 * ViewRankingCalculator::calculate().
 */
final class RankingView extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'ranking_view';

    protected $primaryKey = null;

    protected $casts = [
        'winner' => 'boolean',
        'top_scorer' => 'boolean',
        'final_timestamp' => 'datetime',
    ];

    /**
     * @param  Builder<self>  $query
     */
    public function scopeForLeague(Builder $query, int $leagueId): void
    {
        $query->where('league_id', $leagueId)->orderBy('position');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<League, $this>
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function save(array $options = []): bool
    {
        throw new LogicException('RankingView is read-only. Refresh via ViewRankingCalculator::calculate().');
    }

    public function delete(): ?bool
    {
        throw new LogicException('RankingView is read-only.');
    }
}
