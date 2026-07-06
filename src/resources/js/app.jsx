import './scss/app.scss';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route } from './lib/route';

// Make route() available globally so components can use it
window.route = route;

createInertiaApp({
    resolve: (name) => resolvePageComponent(
        `./pages/${name}.jsx`,
        import.meta.glob('./pages/**/*.jsx')
    ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#f97316',
    },
});
