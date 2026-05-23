<?php

declare(strict_types=1);

namespace App\Models;

use App\Modules\Auth\Models\User;
use Closure;
use Database\Factories\PredictionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Bet
 *
 * @property int $id
 * @property string $sign
 * @property int $home_score
 * @property int $away_score
 * @property int $user_id
 * @property int $game_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Game $game
 * @property-read User $user
 *
 * @method static PredictionFactory factory(...$parameters)
 * @method static Builder|Prediction newModelQuery()
 * @method static Builder|Prediction newQuery()
 * @method static Builder|Prediction query()
 * @method static Builder|Prediction whereAwayResult($value)
 * @method static Builder|Prediction whereAwayScore($value)
 * @method static Builder|Prediction whereCreatedAt($value)
 * @method static Builder|Prediction whereGameId($value)
 * @method static Builder|Prediction whereHomeResult($value)
 * @method static Builder|Prediction whereHomeScore($value)
 * @method static Builder|Prediction whereId($value)
 * @method static Builder|Prediction whereSign($value)
 * @method static Builder|Prediction whereUpdatedAt($value)
 * @method static Builder|Prediction whereUserId($value)
 *
 * @property-read mixed $away_scorer_name
 * @property-read mixed $home_scorer_name
 * @property int|null $home_scorer_id
 * @property int|null $away_scorer_id
 *
 * @method static Builder|Prediction whereAwayScorerId($value)
 * @method static Builder|Prediction whereHomeScorerId($value)
 * @method static Builder|Prediction whereIsAwayOwn($value)
 * @method static Builder|Prediction whereIsHomeOwn($value)
 *
 * @property-read mixed $away_scorer
 * @property-read mixed $home_scorer
 * @property int $league_id
 *
 * @method static Builder|Prediction whereLeagueId($value)
 *
 * @mixin Eloquent
 */
final class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_score',
        'away_score',
        'sign',
        'home_scorer_id',
        'away_scorer_id',
        'user_id',
        'game_id',
        'league_id',
        'created_at',
        'updated_at',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (new Carbon($value))->timezone('Europe/Rome'),
            set: static fn ($value, $attributes) => $attributes['updated_at']
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (new Carbon($value))->timezone('Europe/Rome'),
            set: static fn ($value) => (new Carbon($value))->timezone('Europe/Rome')->format('d-m-Y H:i:s.u')
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function homeScorer(): Attribute
    {
        return Attribute::get(
            get: $this->mapScorer($this->home_scorer_id),
        );
    }

    public function awayScorer(): Attribute
    {
        return Attribute::get(
            get: $this->mapScorer($this->away_scorer_id),
        );
    }

    private function mapScorer(?int $scorerId): Closure
    {
        return static fn () => match ($scorerId) {
            null => 'ammaccabanane',
            -1 => 'Autogol',
            0 => 'No Gol',
            default => Player::find($scorerId)?->displayed_name,
        };
    }
}
