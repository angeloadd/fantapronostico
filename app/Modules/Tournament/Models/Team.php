<?php

declare(strict_types=1);

namespace App\Modules\Tournament\Models;

use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Modules\ApiSport\Dto\TeamsDto;
use App\Modules\ApiSport\Dto\WinnerDto;
use App\Modules\Tournament\Database\Factory\TeamFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $logo
 * @property int $is_national
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Game> $games
 * @property-read int|null $games_count
 * @property-read Collection<int, Player> $players
 * @property-read int|null $players_count
 * @property-read Collection<int, Tournament> $tournaments
 * @property-read int|null $tournaments_count
 * @property-read object{is_away: bool} $pivot
 *
 * @method static TeamFactory factory($count = null, $state = [])
 * @method static Builder|Team newModelQuery()
 * @method static Builder|Team newQuery()
 * @method static Builder|Team query()
 * @method static Builder|Team whereCode($value)
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereIsNational($value)
 * @method static Builder|Team whereLogo($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team whereUpdatedAt($value)
 *
 * @property int $api_id
 *
 * @method static Builder|Team whereApiId($value)
 *
 * @mixin Eloquent
 */
final class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'api_id',
        'name',
        'code',
        'logo',
        'is_national',
    ];

    public static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }

    public static function setWinner(WinnerDto $dto, Tournament $tournament): void
    {
        if (null === $dto->teamApiId) {
            return;
        }

        $team = self::whereApiId($dto->teamApiId)->first();
        if ($team instanceof self) {
            $tournament->teams()->updateExistingPivot($team->id, ['is_winner' => true]);
        }
    }

    public static function upsertTeamsDto(TeamsDto $teamsDto): void
    {
        foreach ($teamsDto->teams() as $teamDto) {
            $team = self::updateOrCreate(['api_id' => $teamDto->apiId], $teamDto->toArray());

            Tournament::attachByApiId($team->id, $teamsDto->tournamentApiId);
        }
    }

    /**
     * @return HasMany<Player, $this>
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class, $this->is_national ? 'national_id' : 'club_id');
    }

    /**
     * @return BelongsToMany<Tournament, $this>
     */
    public function tournaments(): BelongsToMany
    {
        return $this->belongsToMany(Tournament::class);
    }

    /**
     * @return BelongsToMany<Game, $this>
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class);
    }
}
