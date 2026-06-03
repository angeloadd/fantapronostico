<?php

declare(strict_types=1);

namespace App\Modules\League\Models;

use App\Models\Game;
use App\Models\Prediction;
use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PredictionRank extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $table = 'prediction_rank';

    protected $fillable = [
        'user_id',
        'prediction_id',
        'league_id',
        'game_id',
        'is_exact',
        'is_sign',
        'is_home_scorer',
        'is_away_scorer',
        'value_exact',
        'value_sign',
        'value_scorer',
        'total',
        'is_final',
        'predicted_at',
    ];

    protected $casts = [
        'is_exact' => 'boolean',
        'is_sign' => 'boolean',
        'is_home_scorer' => 'boolean',
        'is_away_scorer' => 'boolean',
        'is_final' => 'boolean',
        'predicted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Prediction, $this>
     */
    public function prediction(): BelongsTo
    {
        return $this->belongsTo(Prediction::class);
    }

    /**
     * @return BelongsTo<League, $this>
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
