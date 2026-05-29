<?php

declare(strict_types=1);

namespace App\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Tournament\Models\Team;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Champion
 *
 * @property int $id
 * @property int $user_id
 * @property int $team_id
 * @property int $player_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|Champion newModelQuery()
 * @method static Builder|Champion newQuery()
 * @method static Builder|Champion query()
 * @method static Builder|Champion whereCreatedAt($value)
 * @method static Builder|Champion whereId($value)
 * @method static Builder|Champion wherePlayerId($value)
 * @method static Builder|Champion whereTeamId($value)
 * @method static Builder|Champion whereUpdatedAt($value)
 * @method static Builder|Champion whereUserId($value)
 *
 * @property-read Player $player
 * @property-read Team $team
 * @property-read User $user
 *
 * @mixin Eloquent
 */
final class Champion extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'team_id',
        'player_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<Player, $this>T
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
