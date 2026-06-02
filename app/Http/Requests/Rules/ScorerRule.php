<?php

declare(strict_types=1);

namespace App\Http\Requests\Rules;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ScorerRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $game = request()->route()?->parameter('game')
            ?? request()->route()?->parameter('prediction')?->game;

        if (!$game instanceof Game) {
            return;
        }

        if (null !== $value && $game->isGroupStage()) {
            $fail('Pronostico gol non valido nella fase a gironi');
        }

        if (empty($value) && '0' !== $value && !$game->isGroupStage()) {
            if ('home_scorer_id' === $attribute) {
                $fail('Il campo gol casa non è valido');
            } else {
                $fail('Il campo gol trasferta non è valido');
            }
        }
    }
}
