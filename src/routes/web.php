<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameRoundDefinitionController;
use App\Http\Controllers\GameScoringRuleController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Guest routes (only for non-authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:3,1');

    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/profile/{user}', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile/{user}', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/{user}/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.update-password');

    /*
    |--------------------------------------------------------------------------
    | Games CRUD (admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::resource('games', GameController::class);

        Route::post('/games/{game}/round-definitions', [GameRoundDefinitionController::class, 'store'])
            ->name('games.round-definitions.store');

        Route::put('/games/{game}/round-definitions/{roundDefinition}', [GameRoundDefinitionController::class, 'update'])
            ->name('games.round-definitions.update');

        Route::delete('/games/{game}/round-definitions/{roundDefinition}', [GameRoundDefinitionController::class, 'destroy'])
            ->name('games.round-definitions.destroy');

        Route::post('/games/{game}/scoring-rules', [GameScoringRuleController::class, 'store'])
            ->name('games.scoring-rules.store');

        Route::put('/games/{game}/scoring-rules/{scoringRule}', [GameScoringRuleController::class, 'update'])
            ->name('games.scoring-rules.update');

        Route::delete('/games/{game}/scoring-rules/{scoringRule}', [GameScoringRuleController::class, 'destroy'])
            ->name('games.scoring-rules.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Tournaments
    |--------------------------------------------------------------------------
    */
    Route::get('/tournaments', [TournamentController::class, 'index'])
        ->name('tournaments.index');

    Route::get('/tournaments/create', [TournamentController::class, 'create'])
        ->name('tournaments.create')
        ->middleware('manager');

    Route::post('/tournaments', [TournamentController::class, 'store'])
        ->name('tournaments.store')
        ->middleware('manager');

    Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])
        ->name('tournaments.show');

    Route::post('/tournaments/{tournament}/join', [TournamentController::class, 'join'])
        ->name('tournaments.join');

    Route::post('/tournaments/{tournament}/leave', [TournamentController::class, 'leave'])
        ->name('tournaments.leave');

    Route::post('/tournaments/{tournament}/start', [TournamentController::class, 'start'])
        ->name('tournaments.start')
        ->middleware('manager');

    Route::post('/tournaments/{tournament}/rounds', [TournamentController::class, 'createRound'])
        ->name('tournaments.rounds.store')
        ->middleware('manager');

    Route::post('/tournaments/rounds/{round}/close', [TournamentController::class, 'closeRound'])
        ->name('tournaments.rounds.close')
        ->middleware('manager');

    Route::post('/tournaments/{tournament}/close', [TournamentController::class, 'close'])
        ->name('tournaments.close')
        ->middleware('manager');

    /*
    |--------------------------------------------------------------------------
    | Matches
    |--------------------------------------------------------------------------
    */
    Route::get('/matches', [MatchController::class, 'index'])
        ->name('matches.index');

    Route::get('/matches/{match}', [MatchController::class, 'show'])
        ->name('matches.show');

    Route::post('/matches/rounds/{round}/scores', [MatchController::class, 'updateScore'])
        ->name('matches.rounds.scores.upsert');

    Route::post('/matches/{match}/finish', [MatchController::class, 'playerFinish'])
        ->name('matches.player-finish');

    Route::post('/matches/{match}/close', [MatchController::class, 'close'])
        ->name('matches.close');
});

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
