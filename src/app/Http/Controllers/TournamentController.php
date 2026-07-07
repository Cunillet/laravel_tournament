<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\TournamentRound;
use App\Services\TournamentMatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class TournamentController extends Controller
{
    public function index(): Response
    {
        $tournaments = Tournament::query()
            ->withCount('players')
            ->with('game:id,name', 'creator:id,nickname')
            ->latest()
            ->get();

        return Inertia::render('Tournaments/Index', [
            'tournaments' => $tournaments,
        ]);
    }

    public function create(): Response
    {
        $games = \App\Models\Game::all(['id', 'name']);

        return Inertia::render('Tournaments/Create', [
            'games' => $games,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'game_id' => ['required', 'exists:games,id'],
        ]);

        Tournament::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('tournaments.index')
            ->with('success', 'Torneo creado correctamente.');
    }

    public function show(Tournament $tournament): Response
    {
        $tournament->load([
            'game:id,name',
            'creator:id,nickname',
            'players.user:id,nickname',
            'rounds.matches.gameMatch.players.user:id,nickname',
        ]);

        $tournament->loadCount('players');

        return Inertia::render('Tournaments/Show', [
            'tournament' => $tournament,
        ]);
    }

    public function join(Request $request, Tournament $tournament): RedirectResponse
    {
        if (!$tournament->isPending()) {
            return back()->with('error', 'El torneo ya ha comenzado o está cerrado.');
        }

        $exists = TournamentPlayer::where('tournament_id', $tournament->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ya estás inscrito en este torneo.');
        }

        TournamentPlayer::create([
            'tournament_id' => $tournament->id,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Te has unido al torneo.');
    }

    public function leave(Request $request, Tournament $tournament): RedirectResponse
    {
        if (!$tournament->isPending()) {
            return back()->with('error', 'No puedes salir de un torneo que ya ha comenzado.');
        }

        TournamentPlayer::where('tournament_id', $tournament->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return back()->with('success', 'Has salido del torneo.');
    }

    public function start(Tournament $tournament, TournamentMatchService $service): RedirectResponse
    {
        if (!$tournament->isPending()) {
            return back()->with('error', 'El torneo ya ha sido iniciado o está cerrado.');
        }

        if ($tournament->players()->count() < 2) {
            return back()->with('error', 'Se necesitan al menos 2 jugadores para iniciar.');
        }

        $tournament->update(['status' => 'active']);

        return back()->with('success', 'Torneo iniciado.');
    }

    public function createRound(Request $request, Tournament $tournament, TournamentMatchService $service): RedirectResponse
    {
        try {
            $round = $service->createRound($tournament);

            return redirect()->route('tournaments.show', $tournament)
                ->with('success', "Ronda {$round->round_number} creada con éxito.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function closeRound(Request $request, TournamentRound $round, TournamentMatchService $service): RedirectResponse
    {
        try {
            $service->closeRound($round);
            $message = "Ronda {$round->round_number} cerrada.";

            if ($round->tournament->isClosed()) {
                $message .= ' El torneo se ha cerrado (no hay más combinaciones posibles).';
            }

            return redirect()->route('tournaments.show', $round->tournament)
                ->with('success', $message);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function close(Tournament $tournament, TournamentMatchService $service): RedirectResponse
    {
        $service->closeTournament($tournament);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Torneo cerrado.');
    }
}
