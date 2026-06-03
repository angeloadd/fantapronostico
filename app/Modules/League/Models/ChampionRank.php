<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $league_id
 * @property bool $winner
 * @property bool $top_scorer
 * @property int $value_winner
 * @property int $value_top_scorer
 * @property int $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read League $league
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereLeagueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereTopScorer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereValueTopScorer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereValueWinner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChampionRank whereWinner($value)
 *
 * @mixin \Eloquent
 */
final class ChampionRank extends Model
{
    protected $table = 'champion_rank';

    protected $fillable = [
        'user_id',
        'league_id',
        'winner',
        'top_scorer',
        'value_winner',
        'value_top_scorer',
        'total',
    ];

    protected $casts = [
        'winner' => 'boolean',
        'top_scorer' => 'boolean',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

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
}
