<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use App\Models\Champion;
use App\Models\Prediction;
use App\Modules\Auth\Database\Factory\UserFactory;
use App\Modules\Auth\Enums\RoleEnum;
use App\Modules\League\Models\League;
use DateTimeImmutable;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Auth\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|DateTimeImmutable|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read int|null $tokens_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 *
 * @property-read Collection<int, Prediction> $predictions
 * @property-read int|null $predictions_count
 * @property-read Champion|null $champion
 * @property-read Collection<int, Prediction> $bets
 * @property-read int|null $bets_count
 * @property-read Collection<int, League> $leagues
 * @property-read int|null $leagues_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read bool $admin
 * @property-read bool $mod
 * @property-read object{status: string} $pivot
 * @property int|null $selected_league_id
 * @property-read League|null $selectedLeague
 *
 * @method static Builder|User whereSelectedLeagueId($value)
 *
 * @property string|null $two_factor_confirmed_at
 *
 * @method static Builder<static>|User whereTwoFactorConfirmedAt($value)
 *
 * @property-read League|null $selected_league
 *
 * @mixin Eloquent
 */
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * @return HasMany<Prediction, $this>
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    /**
     * @return HasOne<Champion, $this>
     */
    public function champion(): HasOne
    {
        return $this->hasOne(Champion::class);
    }

    /**
     * @return BelongsToMany<League, $this>
     */
    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class)->withPivot(['status']);
    }

    /**
     * @return HasMany<Role, $this>
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * @return Attribute<bool, never>
     */
    public function admin(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->roles->some(fn (Role $role) => RoleEnum::ADMIN === $role->role)
        );
    }

    /**
     * @return Attribute<bool, never>
     */
    public function mod(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->roles->some(fn (Role $role) => in_array($role->role, [RoleEnum::MOD, RoleEnum::ADMIN], true))
        );
    }

    /**
     * @return Attribute<League|null, never>
     */
    public function selectedLeague(): Attribute
    {
        return Attribute::make(
            get: fn (): ?League => $this->leagues->firstWhere('id', $this->selected_league_id),
            set: fn (League $league) => ['selected_league_id' => $league->id],
        );
    }
}
