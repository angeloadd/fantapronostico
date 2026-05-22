<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GameStatus;
use App\Modules\ApiSport\Dto\GameGoalsDto;
use App\Modules\ApiSport\Dto\GamesDto;
use App\Modules\Tournament\Models\Team;
use Database\Factories\GameFactory;
use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Game
 *
 * @property int $id
 * @property int $tournament_id
 * @property Carbon $started_at
 * @property string $stage
 * @property GameStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team|null $away_team
 * @property-read Team|null $home_team
 * @property-read Collection<int, Player> $players
 * @property-read int|null $players_count
 * @property-read Collection<int, Team> $teams
 * @property-read int|null $teams_count
 * @property-read Tournament $tournament
 *
 * @method static GameFactory factory($count = null, $state = [])
 * @method static Builder|Game newModelQuery()
 * @method static Builder|Game newQuery()
 * @method static Builder|Game query()
 * @method static Builder|Game whereCreatedAt($value)
 * @method static Builder|Game whereId($value)
 * @method static Builder|Game whereStage($value)
 * @method static Builder|Game whereStatus($value)
 * @method static Builder|Game whereTournamentId($value)
 * @method static Builder|Game whereUpdatedAt($value)
 * @method static Builder|Game whereStartedAt($value)
 * @method static Builder|Game fromLatest()
 * @method static Builder|Game lastResults(\DateTimeInterface $now)
 *
 * @property-read Collection<int, GameGoal> $goals
 * @property-read int|null $goals_count
 * @property-read Collection<int, Prediction> $predictions
 * @property-read int|null $predictions_count
 * @property-read int $away_score
 * @property-read int[] $away_scorers
 * @property-read int $home_score
 * @property-read int[] $home_scorers
 * @property-read string $sign
 * @property-read Carbon $predictable_from
 *
 * @mixin Eloquent
 */
final class Game extends Model
{
    use HasFactory;

    private const FINAL = 'final';

    private const GROUP = 'group';

    public mixed $timestamp;

    protected $fillable = [
        'id',
        'tournament_id',
        'stage',
        'status',
        'started_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = ['home_team', 'away_team', 'away_score', 'home_score', 'sign'];

    public static function scopeLastResults(Builder $query, DateTimeInterface $now): Builder
    {
        return $query->fromLatest()
            ->where('started_at', '<', $now)
            ->where('status', 'finished')
            ->limit(4);
    }

    public static function notCompletedToday(): Collection
    {
        return self::where(
            'started_at',
            '>',
            today()->startOfDay()->subHours(4)
        )->where(
            'started_at',
            '<',
            today()->endOfDay()
        )->where('status', 'not_started')->get();
    }

    public static function upsertMany(GamesDto $games): void
    {
        $tournamentId = null;
        foreach ($games->games() as $game) {
            if (null === $tournamentId) {
                $tournamentId = Tournament::whereApiId($game->tournamentApiId)
                    ->firstOrFail()
                    ->id;
            }

            /**
             * @var Game $gameModel
             */
            $gameModel = self::updateOrCreate(['id' => $game->apiId], [
                'id' => $game->apiId,
                'started_at' => $game->startedAt,
                'stage' => $game->stage,
                'status' => $game->status,
                'tournament_id' => $tournamentId,
            ]);

            if ( ! isset($gameModel->home_team, $gameModel->away_team)) {
                $gameModel->teams()
                    ->attach([
                        Team::whereApiId($game->homeTeamApiId)->firstOrFail()->id => ['is_away' => false],
                        Team::whereApiId($game->awayTeamApiId)->firstOrFail()->id => ['is_away' => true],
                    ]);
            }

            $gameModel->save();
        }
    }

    public function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'status' => GameStatus::class,
        ];
    }

    public function goals(): HasMany
    {
        return $this->hasMany(GameGoal::class);
    }

    /**
     * @return BelongsToMany<Player>
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    /**
     * @return BelongsTo<Tournament, Game>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return BelongsToMany<Team>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('is_away');
    }

    public function predictableFrom(): Attribute
    {
        return Attribute::get(
            fn (): Carbon => $this->started_at->subDays($this->isFirstGame() ? 2 : 1)
        );
    }

    /**
     * @return Attribute<Team, Team>
     */
    public function homeTeam(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Team => $this->teams->filter(static fn (Team $team): bool => ! $team->pivot->is_away)->first()
        );
    }

    /**
     * @return Attribute<Team, Team>
     */
    public function awayTeam(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Team => $this->teams->filter(static fn (Team $team): bool => (bool) $team->pivot->is_away)->first()
        );
    }

    public function homeScore(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->goals->filter(
                fn (GameGoal $goal): bool => $goal->player->national_id === $this->home_team->id && ! $goal->is_autogoal
            )->count() + $this->goals->filter(fn (GameGoal $goal): bool => $goal->player->national_id === $this->away_team->id && $goal->is_autogoal)->count(),
        );
    }

    public function awayScore(): Attribute
    {
        return Attribute::get(
            fn (): int => $this->goals->filter(
                fn (GameGoal $goal): bool => $goal->player->national_id === $this->away_team->id && ! $goal->is_autogoal
            )->count() + $this->goals->filter(fn (GameGoal $goal): bool => $goal->player->national_id === $this->home_team->id && $goal->is_autogoal)->count(),
        );
    }

    public function sign(): Attribute
    {
        return Attribute::get(
            function (): string {
                if ($this->home_score > $this->away_score) {
                    return '1';
                }

                if ($this->home_score < $this->away_score) {
                    return '2';
                }

                return 'x';
            }
        );
    }

    public function homeScorers(): Attribute
    {
        return Attribute::get(
            function () {
                $homeScorers = [];
                $this->goals->filter(fn (GameGoal $gameGoal) => $gameGoal->player->national_id === $this->home_team->id && ! $gameGoal->is_autogoal)->each(function (GameGoal $gameGoal) use (&$homeScorers): void {
                    $homeScorers[] = $gameGoal->player->id;
                });
                if ($this->goals->some(fn (GameGoal $gameGoal) => $gameGoal->player->national_id === $this->away_team->id && $gameGoal->is_autogoal)) {
                    $homeScorers[] = -1;
                }

                if (empty($homeScorers)) {
                    return [0];
                }

                return $homeScorers;
            }
        );
    }

    public function awayScorers(): Attribute
    {
        return Attribute::get(
            function () {
                $awayScorers = [];
                $this->goals->filter(fn (GameGoal $gameGoal) => $gameGoal->player->national_id === $this->away_team->id && ! $gameGoal->is_autogoal)->each(function (GameGoal $gameGoal) use (&$awayScorers): void {
                    $awayScorers[] = $gameGoal->player->id;
                });
                if ($this->goals->some(fn (GameGoal $gameGoal) => $gameGoal->player->national_id === $this->home_team->id && $gameGoal->is_autogoal)) {
                    $awayScorers[] = -1;
                }

                if (empty($awayScorers)) {
                    return [0];
                }

                return $awayScorers;
            }
        );
    }

    public function isFirstGame(): bool
    {
        return self::first()->id === $this->id;
    }

    public function isFinal(): bool
    {
        return str_contains(mb_strtolower($this->stage), self::FINAL);
    }

    public function isGroupStage(): bool
    {
        return str_contains(mb_strtolower($this->stage), self::GROUP);
    }

    public function scopeFromLatest($query): void
    {
        $query->orderBy('started_at', 'desc')
            ->orderBy('id', 'desc');
    }

    public function getScoreParsed(string $type): array
    {
        $goals = $this->goals->filter(
            fn (GameGoal $goal) => $goal->player->national_id === $this->{$type . '_team'}->id ||
                    $goal->player->club_id === $this->{$type . '_team'}->id
        )->map(
            static function (GameGoal $goal) {
                if ($goal->is_autogoal) {
                    return 'AutoGol';
                }

                return $goal->player->displayed_name;
            }
        )->toArray();

        if (0 === count($goals)) {
            $goals[] = 'NoGoal';
        }

        return $goals;
    }

    public function addGameGoals(GameGoalsDto $gameGoals): void
    {
        foreach ($gameGoals->gameGoals() as $gameGoal) {
            $this->goals()->firstOrCreate(
                [
                    'game_id' => $this->id,
                    'player_id' => $gameGoal->playerApiId,
                    'scored_at' => $gameGoal->scoredAt,
                ],
                [
                    'player_id' => $gameGoal->playerApiId,
                    'is_autogoal' => $gameGoal->isOwnGoal,
                    'scored_at' => $gameGoal->scoredAt,
                ]
            );
        }
    }

    public function isNotPredictableYet(): bool
    {
        return $this->predictable_from->isFuture();
    }

    protected static function booted(): void
    {
        self::addGlobalScope(
            'orderedByGameDate',
            static function (Builder $builder): void {
                $builder->orderBy('started_at')
                    ->orderBy('id');
            }
        );
    }
}
