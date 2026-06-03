<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ChampionRank extends Model
{
    protected $table = 'champion_rank';

    protected $fillable = [
        'user_id',
        'league_id',
        'winner',
        'top_scorer',
        'value_winner',
        'value_top_scorer',
        'total',
    ];

    protected $casts = [
        'winner' => 'boolean',
        'top_scorer' => 'boolean',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<League, $this>
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }
}
