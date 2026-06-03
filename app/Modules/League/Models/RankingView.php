<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * Read-only model backed by the ranking_view materialized view.
 *
 * Use RankingView::forLeague($leagueId)->get() to read rankings.
 * Writes are not supported — the underlying view is refreshed via
 * ViewRankingCalculator::calculate().
 *
 * @property int|null $position
 * @property int|null $user_id
 * @property int|null $league_id
 * @property string|null $user_name
 * @property int|null $total
 * @property int|null $results
 * @property int|null $signs
 * @property int|null $scorers
 * @property int|null $final_total
 * @property Carbon|null $final_timestamp
 * @property bool|null $winner
 * @property bool|null $top_scorer
 * @property-read League|null $league
 * @property-read User|null $user
 *
 * @method static Builder<static>|RankingView forLeague(int $leagueId)
 * @method static Builder<static>|RankingView newModelQuery()
 * @method static Builder<static>|RankingView newQuery()
 * @method static Builder<static>|RankingView query()
 * @method static Builder<static>|RankingView whereFinalTimestamp($value)
 * @method static Builder<static>|RankingView whereFinalTotal($value)
 * @method static Builder<static>|RankingView whereLeagueId($value)
 * @method static Builder<static>|RankingView wherePosition($value)
 * @method static Builder<static>|RankingView whereResults($value)
 * @method static Builder<static>|RankingView whereScorers($value)
 * @method static Builder<static>|RankingView whereSigns($value)
 * @method static Builder<static>|RankingView whereTopScorer($value)
 * @method static Builder<static>|RankingView whereTotal($value)
 * @method static Builder<static>|RankingView whereUserId($value)
 * @method static Builder<static>|RankingView whereUserName($value)
 * @method static Builder<static>|RankingView whereWinner($value)
 *
 * @mixin \Eloquent
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
