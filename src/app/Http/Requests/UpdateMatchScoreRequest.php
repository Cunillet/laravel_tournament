<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateMatchScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'match_player_id' => ['required', 'exists:match_players,id'],
            'scoring_rule_id' => ['required', 'exists:scoring_rules,id'],
            'score'           => ['required', 'numeric'],
        ];
    }
}
