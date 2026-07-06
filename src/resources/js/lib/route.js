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

    if (typeof params === 'number' || typeof params === 'string') {
        params = { user: params };
    }

    let url = pattern;

    for (const [key, value] of Object.entries(params)) {
        url = url.replace(`{${key}}`, String(value));
    }

    return url;
}
