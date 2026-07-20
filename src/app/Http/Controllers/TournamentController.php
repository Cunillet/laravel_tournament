<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\TournamentRound;
use App\Services\StandingsService;
use App\Services\TournamentMatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class TournamentController extends Controller
{
    /**
     * List all tournaments.
     */
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

    /**
     * Show the tournament creation form.
     */
    public function create(): Response
    {
        $games = \App\Models\Game::all(['id', 'name']);

        return Inertia::render('Tournaments/Create', [
            'games' => $games,
        ]);
    }

    /**
     * Store a new tournament.
     */
    public function store(StoreTournamentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Tournament::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('tournaments.index')
            ->with('success', 'Torneo creado correctamente.');
    }

    /**
     * Display the tournament details page with standings.
     */
    public function show(Tournament $tournament, StandingsService $standingsService): Response
    {
        $tournament->load([
            'game:id,name',
            'creator:id,nickname',
            'players.user:id,nickname',
            'rounds.matches.gameMatch.players.user:id,nickname',
        ]);

        $tournament->loadCount('players');

        $standings = $standingsService->buildStandings($tournament);

        return Inertia::render('Tournaments/Show', [
            'tournament' => $tournament,
            'standings' => $standings,
        ]);
    }

    /**
     * Join the tournament as the authenticated user.
     */
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

    /**
     * Leave the tournament as the authenticated user.
     */
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

    /**
     * Start the tournament, changing its status to 'active'.
     */
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

    /**
     * Create a new round in the tournament with match pairings.
     */
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

    /**
     * Close the current active round.
     *
     * If no more pairings are possible, the tournament is also closed.
     */
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

    /**
     * Force-close the tournament regardless of round status.
     */
    public function close(Tournament $tournament, TournamentMatchService $service): RedirectResponse
    {
        $service->closeTournament($tournament);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Torneo cerrado.');
    }

    /**
     * Delete the tournament and all associated data.
     *
     * Only accessible by admin users via the admin middleware.
     */
    public function destroy(Tournament $tournament): RedirectResponse
    {
        $tournament->purge();

        return redirect()->route('tournaments.index')
            ->with('success', 'Torneo eliminado correctamente.');
    }
}
