<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Models\Game;
use App\Models\Prediction;
use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $prediction_id
 * @property int $league_id
 * @property int $game_id
 * @property bool $is_exact
 * @property bool $is_sign
 * @property bool $is_home_scorer
 * @property bool $is_away_scorer
 * @property int $value_exact
 * @property int $value_sign
 * @property int $value_scorer
 * @property int $total
 * @property bool $is_final
 * @property Carbon $predicted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Game $game
 * @property-read League $league
 * @property-read Prediction $prediction
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereIsAwayScorer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereIsExact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereIsFinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereIsHomeScorer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereIsSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereLeagueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank wherePredictedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank wherePredictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereValueExact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereValueScorer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PredictionRank whereValueSign($value)
 *
 * @mixin \Eloquent
 */
final class PredictionRank extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $table = 'prediction_rank';

    protected $fillable = [
        'user_id',
        'prediction_id',
        'league_id',
        'game_id',
        'is_exact',
        'is_sign',
        'is_home_scorer',
        'is_away_scorer',
        'value_exact',
        'value_sign',
        'value_scorer',
        'total',
        'is_final',
        'predicted_at',
    ];

    protected $casts = [
        'is_exact' => 'boolean',
        'is_sign' => 'boolean',
        'is_home_scorer' => 'boolean',
        'is_away_scorer' => 'boolean',
        'is_final' => 'boolean',
        'predicted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Prediction, $this>
     */
    public function prediction(): BelongsTo
    {
        return $this->belongsTo(Prediction::class);
    }

    /**
     * @return BelongsTo<League, $this>
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
