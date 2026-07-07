// Route helper for Inertia.
// Replaces the PHP route() function in JavaScript.
// All named routes must be registered here with {param} placeholders.

const routes = {
    'home': '/',
    'login': '/login',
    'register': '/register',
    'logout': '/logout',
    'profile.show': '/profile/{user}',
    'profile.edit': '/profile/{user}/edit',
    'profile.update': '/profile/{user}',
    'profile.update-password': '/profile/{user}/password',
    'games.index': '/games',
    'games.create': '/games/create',
    'games.store': '/games',
    'games.show': '/games/{game}',
    'games.edit': '/games/{game}/edit',
    'games.update': '/games/{game}',
    'games.destroy': '/games/{game}',
    'games.round-definitions.store': '/games/{game}/round-definitions',
    'games.round-definitions.update': '/games/{game}/round-definitions/{roundDefinition}',
    'games.round-definitions.destroy': '/games/{game}/round-definitions/{roundDefinition}',
    'games.scoring-rules.store': '/games/{game}/scoring-rules',
    'games.scoring-rules.update': '/games/{game}/scoring-rules/{scoringRule}',
    'games.scoring-rules.destroy': '/games/{game}/scoring-rules/{scoringRule}',
    'tournaments.index': '/tournaments',
    'tournaments.create': '/tournaments/create',
    'tournaments.store': '/tournaments',
    'tournaments.show': '/tournaments/{tournament}',
    'tournaments.join': '/tournaments/{tournament}/join',
    'tournaments.leave': '/tournaments/{tournament}/leave',
    'tournaments.start': '/tournaments/{tournament}/start',
    'tournaments.rounds.store': '/tournaments/{tournament}/rounds',
    'tournaments.rounds.close': '/tournaments/rounds/{round}/close',
    'tournaments.close': '/tournaments/{tournament}/close',
    'matches.index': '/matches',
    'matches.show': '/matches/{match}',
    'matches.rounds.scores.upsert': '/matches/rounds/{round}/scores',
    'matches.player-finish': '/matches/{match}/finish',
    'matches.close': '/matches/{match}/close',
};

/**
 * Generate a URL for a named route.
 *
 * @param {string} name - The route name.
 * @param {object|number|string} params - Route parameters or single param value.
 * @returns {string} The generated URL.
 */
export function route(name, params = {}) {
    const pattern = routes[name];

    if (!pattern) {
        throw new Error(`[route] "${name}" not found.`);
    }

    // Extract parameter names from pattern like /games/{game}/rounds/{round}
    const paramNames = [...pattern.matchAll(/\{(\w+)\}/g)].map(m => m[1]);

    if (typeof params === 'number' || typeof params === 'string') {
        if (paramNames.length === 1) {
            params = { [paramNames[0]]: params };
        } else {
            throw new Error(
                `[route] "${name}" expects ${paramNames.length} parameters: ${paramNames.join(', ')}`
            );
        }
    }

    let url = pattern;

    for (const [key, value] of Object.entries(params)) {
        url = url.replace(`{${key}}`, String(value));
    }

    return url;
}
