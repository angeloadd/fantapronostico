<?php

declare(strict_types=1);

namespace App\Models;

use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Database\Factories\TournamentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tournament
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property int $is_cup
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Game> $games
 * @property-read int|null $games_count
 * @property-read Collection<int, Player> $players
 * @property-read int|null $players_count
 * @property-read Collection<int, Team> $teams
 * @property-read int|null $teams_count
 *
 * @method static TournamentFactory factory($count = null, $state = [])
 * @method static Builder|Tournament newModelQuery()
 * @method static Builder|Tournament newQuery()
 * @method static Builder|Tournament query()
 * @method static Builder|Tournament whereCountry($value)
 * @method static Builder|Tournament whereCreatedAt($value)
 * @method static Builder|Tournament whereId($value)
 * @method static Builder|Tournament whereIsCup($value)
 * @method static Builder|Tournament whereName($value)
 * @method static Builder|Tournament whereUpdatedAt($value)
 *
 * @property string $logo
 * @property int $season
 * @property int $api_id
 * @property Carbon $started_at
 * @property Carbon $final_started_at
 *
 * @method static Builder|Tournament whereApiId($value)
 * @method static Builder|Tournament whereFinalStartedAt($value)
 * @method static Builder|Tournament whereLogo($value)
 * @method static Builder|Tournament whereSeason($value)
 * @method static Builder|Tournament whereStartedAt($value)
 *
 * @property-read Collection<int, League> $leagues
 * @property-read int|null $leagues_count
 * @property Carbon|null $knockouts_started_at
 *
 * @method static Builder<static>|Tournament whereKnockoutsStartedAt($value)
 *
 * @mixin Eloquent
 */
final class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'season',
        'api_id',
        'started_at',
        'country',
        'is_cup',
        'final_started_at',
        'knockouts_started_at',
    ];

    public static function attachByApiId(int $teamId, int $tournamentApiId): void
    {
        $tournament = self::whereApiId($tournamentApiId)->first();

        if (0 === $tournament?->teams->where('id', $teamId)->count()) {
            $tournament?->teams()?->attach($teamId);
        }
    }

    /**
     * @return BelongsToMany<Team, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('is_winner');
    }

    /**
     * @return BelongsToMany<Player, $this>
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)->withPivot('is_top_scorer');
    }

    /**
     * @return HasMany<Game, $this>
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    /**
     * @return HasMany<League, $this>
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'final_started_at' => 'datetime',
            'started_at' => 'datetime',
            'knockouts_started_at' => 'datetime',
        ];
    }
}
