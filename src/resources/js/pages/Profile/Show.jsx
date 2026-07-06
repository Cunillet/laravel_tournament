import { Head, Link } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Show({ auth }) {
    const user = auth.user;

    return (
        <>
            <Head title="Mi perfil" />
            <Layout auth={auth}>
                <div className="profile-page">
                    <div className="profile-card">
                        <div className="profile-card__avatar">
                            {user.nickname.charAt(0).toUpperCase()}
                        </div>

                        <h1 className="profile-card__title">{user.nickname}</h1>
                        <p className="profile-card__email">{user.email}</p>

                        <div className="profile-card__info">
                            <div className="profile-card__info-item">
                                <span className="profile-card__info-label">Miembro desde</span>
                                <span className="profile-card__info-value">
                                    {user.created_at}
                                </span>
                            </div>
                            <div className="profile-card__info-item">
                                <span className="profile-card__info-label">Última actualización</span>
                                <span className="profile-card__info-value">
                                    {user.updated_at}
                                </span>
                            </div>
                        </div>

                        <div className="profile-card__actions">
                            <Link
                                href={route('profile.edit', { user: user.id })}
                                className="btn btn--primary"
                            >
                                Editar perfil
                            </Link>
                        </div>
                    </div>
                </div>
            </Layout>
        </>
    );
}
