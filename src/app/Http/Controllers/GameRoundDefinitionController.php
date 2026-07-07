<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoundDefinitionRequest;
use App\Http\Requests\UpdateRoundDefinitionRequest;
use App\Models\Game;
use App\Models\RoundDefinition;
use Illuminate\Http\RedirectResponse;

final class GameRoundDefinitionController extends Controller
{
    public function store(StoreRoundDefinitionRequest $request, Game $game): RedirectResponse
    {
        $game->rounds()->create($request->validated());

        return redirect()->route('games.edit', $game)
            ->with('success', 'Ronda añadida correctamente.');
    }

    public function update(UpdateRoundDefinitionRequest $request, Game $game, RoundDefinition $roundDefinition): RedirectResponse
    {
        $roundDefinition->update($request->validated());

        return redirect()->route('games.edit', $game)
            ->with('success', 'Ronda actualizada correctamente.');
    }

    public function destroy(Game $game, RoundDefinition $roundDefinition): RedirectResponse
    {
        $roundDefinition->delete();

        return redirect()->route('games.edit', $game)
            ->with('success', 'Ronda eliminada correctamente.');
    }
}
