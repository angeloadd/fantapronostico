<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GameGoalFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\GameGoal
 *
 * @property int $id
 * @property int $game_id
 * @property int $player_id
 * @property int $scored_at
 * @property int $is_autogoal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Game $game
 * @property-read Player $player
 *
 * @method static Builder|GameGoal newModelQuery()
 * @method static Builder|GameGoal newQuery()
 * @method static Builder|GameGoal query()
 * @method static Builder|GameGoal whereCreatedAt($value)
 * @method static Builder|GameGoal whereGameId($value)
 * @method static Builder|GameGoal whereId($value)
 * @method static Builder|GameGoal whereIsAutogoal($value)
 * @method static Builder|GameGoal wherePlayerId($value)
 * @method static Builder|GameGoal whereScoredAt($value)
 * @method static Builder|GameGoal whereUpdatedAt($value)
 * @method static GameGoalFactory factory($count = null, $state = [])
 *
 * @mixin Eloquent
 */
final class GameGoal extends Model
{
    /** @use HasFactory<GameGoalFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'game_id',
        'player_id',
        'scored_at',
        'is_autogoal',
    ];

    protected $casts = [
        'is_autogoal' => 'boolean',
    ];

    /**
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
