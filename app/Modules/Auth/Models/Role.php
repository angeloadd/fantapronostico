<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Enums\RoleEnum;
use App\Modules\League\Models\League;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property RoleEnum $role
 * @property int|null $league_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read League|null $leagues
 * @property-read User $user
 *
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereLeagueId($value)
 * @method static Builder|Role whereRole($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role whereUserId($value)
 *
 * @mixin Eloquent
 */
final class Role extends Model
{
    protected $fillable = [
        'role',
        'user_id',
        'league_id',
    ];

    public function casts(): array
    {
        return [
            'role' => RoleEnum::class,
        ];
    }

    /**
     * @return BelongsTo<League, $this>
     */
    public function leagues(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
