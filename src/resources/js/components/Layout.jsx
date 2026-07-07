import { Link, router } from '@inertiajs/react';

export default function Layout({ children, auth }) {
    const handleLogout = (e) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    return (
        <div className="layout">
            <header className="header">
                <div className="header__container">
                    <Link href="/" className="header__logo">
                        <span className="header__logo-icon">DD</span>
                        <span className="header__logo-text">Tournaments</span>
                    </Link>

                    <nav className="header__nav">
                        {auth.user ? (
                            <div className="header__nav-items">
                                        <Link
                                            href={route('tournaments.index')}
                                            className="header__nav-link"
                                        >
                                            Torneos
                                        </Link>
                                        <Link
                                            href={route('matches.index')}
                                            className="header__nav-link"
                                        >
                                            Partidas
                                        </Link>
                                        {auth.user.role === 0 && (
                                            <Link
                                                href={route('games.index')}
                                                className="header__nav-link"
                                            >
                                                Juegos
                                            </Link>
                                        )}
                                        <Link
                                    href={route('profile.show', { user: auth.user.id })}
                                    className="header__nav-link"
                                >
                                    Perfil
                                </Link>
                                <a
                                    href={route('logout')}
                                    className="header__nav-link header__nav-link--logout"
                                    onClick={handleLogout}
                                >
                                    Cerrar sesión
                                </a>
                            </div>
                        ) : (
                            <div className="header__nav-items">
                                <Link
                                    href={route('login')}
                                    className="header__nav-link"
                                >
                                    Iniciar sesión
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="header__nav-link header__nav-link--register"
                                >
                                    Registrarse
                                </Link>
                            </div>
                        )}
                    </nav>
                </div>
            </header>

            <main className="main">
                {children}
            </main>
        </div>
    );
}
