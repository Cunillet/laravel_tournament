<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\Game;
use App\Models\ScoringSystem;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class GameController extends Controller
{
    public function index(): Response
    {
        $games = Game::query()
            ->withCount('rounds', 'scoringRules')
            ->latest()
            ->get();

        return Inertia::render('Games/Index', [
            'games' => $games,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Games/Create');
    }

    public function store(StoreGameRequest $request): RedirectResponse
    {
        Game::create($request->validated());

        return redirect()->route('games.index')
            ->with('success', 'Juego creado correctamente.');
    }

    public function show(Game $game): Response
    {
        $game->load(['rounds', 'scoringRules.scoringSystem']);

        return Inertia::render('Games/Show', [
            'game' => $game,
        ]);
    }

    public function edit(Game $game): Response
    {
        $game->load(['rounds', 'scoringRules.scoringSystem']);

        return Inertia::render('Games/Edit', [
            'game' => $game,
            'scoringSystems' => ScoringSystem::all(),
        ]);
    }

    public function update(UpdateGameRequest $request, Game $game): RedirectResponse
    {
        $game->update($request->validated());

        return redirect()->route('games.index')
            ->with('success', 'Juego actualizado correctamente.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return redirect()->route('games.index')
            ->with('success', 'Juego eliminado correctamente.');
    }
}
