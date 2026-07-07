<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchRound;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentPlayer;
use App\Models\TournamentRound;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class TournamentMatchService
{
    /**
     * Create a new round in the tournament by pairing players.
     */
    public function createRound(Tournament $tournament): TournamentRound
    {
        if (!$tournament->isActive()) {
            throw new \RuntimeException('El torneo no está activo.');
        }

        $roundNumber = $tournament->rounds()->count() + 1;

        /** @var Collection<int, TournamentPlayer> */
        $players = $tournament->players()->with('user')->get();

        if ($players->count() < 2) {
            throw new \RuntimeException('Se necesitan al menos 2 jugadores.');
        }

        $playedPairs = $this->getPlayedPairs($tournament);
        $pairs = $this->generatePairs($players, $playedPairs);

        if (empty($pairs)) {
            throw new \RuntimeException('No hay más combinaciones posibles de emparejamientos.');
        }

        return DB::transaction(function () use ($tournament, $roundNumber, $pairs): TournamentRound {
            $round = TournamentRound::create([
                'tournament_id' => $tournament->id,
                'round_number' => $roundNumber,
                'status' => 'active',
            ]);

            foreach ($pairs as $pair) {
                $match = $this->createMatchFromPair($tournament->game, $pair);
                TournamentMatch::create([
                    'tournament_round_id' => $round->id,
                    'game_match_id' => $match->id,
                ]);
            }

            return $round;
        });
    }

    /**
     * Close the current active round. If no more pairings possible, close tournament.
     */
    public function closeRound(TournamentRound $round): void
    {
        $round->update(['status' => 'closed']);

        $tournament = $round->tournament;
        $playedPairs = $this->getPlayedPairs($tournament);
        $players = $tournament->players()->get();

        $remaining = $this->generatePairs($players, $playedPairs);

        if (empty($remaining)) {
            $tournament->update(['status' => 'closed']);
        }
    }

    /**
     * Close the tournament entirely.
     */
    public function closeTournament(Tournament $tournament): void
    {
        $tournament->update(['status' => 'closed']);
    }

    /**
     * @return array<string, true>
     */
    private function getPlayedPairs(Tournament $tournament): array
    {
        $pairs = [];

        $tournamentMatchIds = TournamentMatch::query()
            ->whereHas('tournamentRound', fn ($q) => $q->where('tournament_id', $tournament->id))
            ->pluck('game_match_id');

        $matchPlayers = MatchPlayer::query()
            ->whereIn('game_match_id', $tournamentMatchIds)
            ->get()
            ->groupBy('game_match_id');

        foreach ($matchPlayers as $players) {
            $playerIds = $players->pluck('user_id')->sort()->values();
            for ($i = 0; $i < $playerIds->count(); $i++) {
                for ($j = $i + 1; $j < $playerIds->count(); $j++) {
                    $pairs[$playerIds[$i] . '-' . $playerIds[$j]] = true;
                }
            }
        }

        return $pairs;
    }

    /**
     * @param Collection<int, TournamentPlayer> $players
     * @param array<string, true> $playedPairs
     * @return array<int, array{0: TournamentPlayer, 1: TournamentPlayer}>
     */
    private function generatePairs(Collection $players, array $playedPairs): array
    {
        $sorted = $players->sortBy('user_id')->values();
        $pairs = [];
        $used = [];

        for ($i = 0; $i < $sorted->count() - 1; $i++) {
            if (isset($used[$i])) {
                continue;
            }

            for ($j = $i + 1; $j < $sorted->count(); $j++) {
                if (isset($used[$j])) {
                    continue;
                }

                $a = $sorted[$i]->user_id;
                $b = $sorted[$j]->user_id;
                $key = ($a < $b ? $a . '-' . $b : $b . '-' . $a);

                if (!isset($playedPairs[$key])) {
                    $pairs[] = [$sorted[$i], $sorted[$j]];
                    $used[$i] = true;
                    $used[$j] = true;
                    break;
                }
            }
        }

        return $pairs;
    }

    /**
     * @param array{0: TournamentPlayer, 1: TournamentPlayer} $pair
     */
    private function createMatchFromPair(Game $game, array $pair): GameMatch
    {
        $match = GameMatch::create([
            'game_id' => $game->id,
            'status' => 'pending',
        ]);

        MatchPlayer::create(['game_match_id' => $match->id, 'user_id' => $pair[0]->user_id]);
        MatchPlayer::create(['game_match_id' => $match->id, 'user_id' => $pair[1]->user_id]);

        $roundDefs = $game->rounds()->get();
        $order = 0;

        foreach ($roundDefs as $roundDef) {
            $repetitions = $roundDef->rounds_count ?? 1;
            for ($i = 0; $i < $repetitions; $i++) {
                MatchRound::create([
                    'game_match_id' => $match->id,
                    'round_id' => $roundDef->id,
                    'status' => 'pending',
                    'order' => $order++,
                ]);
            }
        }

        return $match;
    }
}
