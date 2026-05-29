<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Rules\ScorerRule;
use Illuminate\Foundation\Http\FormRequest;

final class CreatePredictionRequest extends FormRequest
{
    public const HOME_SCORE = 'home_score';

    public const AWAY_SCORE = 'away_score';

    public const SIGN = 'sign';

    public const HOME_SCORER_ID = 'home_scorer_id';

    public const AWAY_SCORER_ID = 'away_scorer_id';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|ScorerRule>>
     */
    public function rules(): array
    {
        return [
            self::HOME_SCORE => ['required', 'integer', 'numeric'],
            self::AWAY_SCORE => ['required', 'integer', 'numeric'],
            self::SIGN => ['required'],
            self::HOME_SCORER_ID => new ScorerRule(),
            self::AWAY_SCORER_ID => new ScorerRule(),
        ];
    }

    public function messages(): array
    {
        return [
            'home_score.required' => 'Il campo risultato esatto casa è richiesto.',
            'home_score.integer' => 'Il campo risultato esatto casa deve essere numerico',
            'home_score.numeric' => 'Il campo risultato esatto casa deve essere numerico.',
            'away_score.required' => 'Il campo risultato esatto trasferta è richiesto.',
            'away_score.integer' => 'Il campo risultato esatto trasferta deve essere numerico',
            'away_score.numeric' => 'Il campo risultato esatto trasferta deve essere numerico',
            'sign.required' => 'Il campo segno è richiesto',
            'home_scorer_id.required' => 'Il campo gol casa è richiesto',
            'away_scorer_id.required' => 'Il campo gol trasferta è richiesto',
        ];
    }
}
