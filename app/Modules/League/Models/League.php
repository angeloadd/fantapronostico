<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tournament_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Tournament $tournament
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|League newModelQuery()
 * @method static Builder|League newQuery()
 * @method static Builder|League query()
 * @method static Builder|League whereCreatedAt($value)
 * @method static Builder|League whereId($value)
 * @method static Builder|League whereName($value)
 * @method static Builder|League whereTournamentId($value)
 * @method static Builder|League whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class League extends Model
{
    protected $fillable = [
        'tournament_id',
        'name',
    ];

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['status']);
    }

    /**
     * @return BelongsTo<Tournament, $this>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
