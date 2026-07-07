<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\MatchScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class MatchController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $matches = GameMatch::query()
            ->with('game:id,name')
            ->whereHas('players', fn ($q) => $q->where('user_id', $user->id))
            ->orWhereDoesntHave('players')
            ->latest()
            ->get();

        return Inertia::render('Matches/Index', [
            'matches' => $matches,
        ]);
    }

    public function show(Request $request, GameMatch $match): Response
    {
        if (!$this->isPlayer($request, $match)) {
            abort(403, 'No eres jugador de esta partida.');
        }

        $match->load([
            'game:id,name,description',
            'players.user:id,nickname',
            'rounds.round',
            'rounds.scores',
        ]);

        // Attach scoring rules per match round: round-specific + global (null round_id)
        foreach ($match->rounds as $mr) {
            $mr->round->scoringRules = \App\Models\ScoringRule::query()
                ->where('game_id', $match->game_id)
                ->where(function ($q) use ($mr) {
                    $q->where('round_id', $mr->round_id)
                        ->orWhereNull('round_id');
                })
                ->orderBy('priority')
                ->get();
        }

        // Find the match_player for the current user
        $currentPlayer = $match->players
            ->firstWhere('user_id', $request->user()->id);

        return Inertia::render('Matches/Show', [
            'match' => $match,
            'currentPlayerId' => $currentPlayer?->id,
            'currentPlayerFinished' => $currentPlayer?->finished_at !== null,
        ]);
    }

    public function updateScore(Request $request, MatchRound $round): JsonResponse|RedirectResponse
    {
        $match = $round->gameMatch;

        if ($match->status !== 'pending') {
            return back()->with('error', 'La partida está cerrada.');
        }

        $validated = $request->validate([
            'match_player_id' => ['required', 'exists:match_players,id'],
            'scoring_rule_id' => ['required', 'exists:scoring_rules,id'],
            'score' => ['required', 'numeric'],
        ]);

        // Ensure the player belongs to this match
        $player = MatchPlayer::findOrFail($validated['match_player_id']);
        if ($player->game_match_id !== $round->game_match_id) {
            throw ValidationException::withMessages([
                'match_player_id' => 'El jugador no pertenece a esta partida.',
            ]);
        }

        // Ensure the scoring rule belongs to this round (round-specific or global)
        $rule = \App\Models\ScoringRule::query()
            ->where('game_id', $match->game_id)
            ->where(function ($q) use ($round) {
                $q->where('round_id', $round->round_id)
                    ->orWhereNull('round_id');
            })
            ->where('id', $validated['scoring_rule_id'])
            ->exists();

        if (!$rule) {
            throw ValidationException::withMessages([
                'scoring_rule_id' => 'La regla de puntuación no pertenece a esta ronda.',
            ]);
        }

        MatchScore::updateOrCreate(
            [
                'match_round_id' => $round->id,
                'match_player_id' => $validated['match_player_id'],
                'scoring_rule_id' => $validated['scoring_rule_id'],
            ],
            [
                'score' => $validated['score'],
            ]
        );

        return back();
    }

    public function playerFinish(Request $request, GameMatch $match): RedirectResponse
    {
        $player = $match->players()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($player->finished_at !== null) {
            return back()->with('info', 'Ya has finalizado tu puntuación.');
        }

        $player->update(['finished_at' => now()]);

        // Check if all players have finished → close the match
        $allFinished = $match->players()
            ->whereNull('finished_at')
            ->doesntExist();

        if ($allFinished) {
            $match->update(['status' => 'completed']);
        }

        return back();
    }

    public function close(Request $request, GameMatch $match): RedirectResponse
    {
        if ($match->status !== 'pending') {
            return back()->with('error', 'La partida ya está cerrada.');
        }

        $match->update(['status' => 'completed']);

        return redirect()->route('matches.show', $match)
            ->with('success', 'Partida finalizada.');
    }

    private function isPlayer(Request $request, GameMatch $match): bool
    {
        $user = $request->user();

        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        return $match->players()
            ->where('user_id', $user->id)
            ->exists();
    }
}
