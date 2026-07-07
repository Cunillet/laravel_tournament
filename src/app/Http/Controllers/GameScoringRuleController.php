<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreScoringRuleRequest;
use App\Http\Requests\UpdateScoringRuleRequest;
use App\Models\Game;
use App\Models\ScoringRule;
use Illuminate\Http\RedirectResponse;

final class GameScoringRuleController extends Controller
{
    public function store(StoreScoringRuleRequest $request, Game $game): RedirectResponse
    {
        $game->scoringRules()->create($request->validated());

        return redirect()->route('games.edit', $game)
            ->with('success', 'Norma de puntuación añadida correctamente.');
    }

    public function update(UpdateScoringRuleRequest $request, Game $game, ScoringRule $scoringRule): RedirectResponse
    {
        $scoringRule->update($request->validated());

        return redirect()->route('games.edit', $game)
            ->with('success', 'Norma de puntuación actualizada correctamente.');
    }

    public function destroy(Game $game, ScoringRule $scoringRule): RedirectResponse
    {
        $scoringRule->delete();

        return redirect()->route('games.edit', $game)
            ->with('success', 'Norma de puntuación eliminada correctamente.');
    }
}
