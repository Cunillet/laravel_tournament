import { Link, Head } from '@inertiajs/react';
import Layout from '../components/Layout';

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Bienvenido" />
            <Layout auth={auth}>
                <div className="hero">
                    <div className="hero__container">
                        <div className="hero__content">
                            <h1 className="hero__title">
                                DD_Tournaments
                            </h1>
                            <p className="hero__subtitle">
                                La plataforma definitiva para organizar y gestionar
                                tus torneos de manera sencilla y eficiente.
                            </p>
                            {!auth.user && (
                                <div className="hero__actions">
                                    <Link href={route('register')} className="btn btn--primary">
                                        Comenzar ahora
                                    </Link>
                                    <Link href={route('login')} className="btn btn--secondary">
                                        Ya tengo cuenta
                                    </Link>
                                </div>
                            )}
                            {auth.user && (
                                <div className="hero__actions">
                                    <Link
                                        href={route('profile.show', { user: auth.user.id })}
                                        className="btn btn--primary"
                                    >
                                        Ir a mi perfil
                                    </Link>
                                </div>
                            )}
                        </div>

                        <div className="hero__features">
                            <div className="feature-card">
                                <Link
                                    href={route('tournaments.index')}
                                    className="text-decoration-none"
                                >
                                    <div className="feature-card__icon">🏆</div>
                                        <h3 className="feature-card__title">
                                            Crea Torneos
                                        </h3>
                                    <p className="feature-card__text">
                                        Organiza torneos personalizados con las reglas que tú elijas.
                                    </p>
                                </Link>
                            </div>
                            <div className="feature-card">
                                <div className="feature-card__icon">👥</div>
                                <h3 className="feature-card__title">Gestiona Equipos</h3>
                                <p className="feature-card__text">
                                    Administra participantes y equipos de forma intuitiva.
                                </p>
                            </div>
                            <div className="feature-card">
                                <div className="feature-card__icon">📊</div>
                                <h3 className="feature-card__title">Sigue Resultados</h3>
                                <p className="feature-card__text">
                                    Visualiza estadísticas y resultados en tiempo real.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </Layout>
        </>
    );
}
