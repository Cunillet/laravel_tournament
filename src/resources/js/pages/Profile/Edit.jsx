import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Edit({ auth, errors: initialErrors = {}, flash = {} }) {
    const user = auth.user;

    const [values, setValues] = useState({
        nickname: user.nickname,
        email: user.email,
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const [errors, setErrors] = useState(initialErrors);
    const [processing, setProcessing] = useState(false);
    const [success, setSuccess] = useState(flash.success);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setValues((prev) => ({ ...prev, [name]: value }));
        setSuccess(false);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});
        setSuccess(false);

        router.put(route('profile.update', { user: user.id }), values, {
            onError: (errs) => {
                setErrors(errs);
                setProcessing(false);
            },
            onSuccess: () => {
                setSuccess(true);
                setProcessing(false);
                setValues((prev) => ({
                    ...prev,
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }));
            },
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title="Editar perfil" />
            <Layout auth={auth}>
                <div className="profile-page">
                    <div className="profile-card">
                        <h1 className="profile-card__title">Editar perfil</h1>
                        <p className="profile-card__subtitle">
                            Actualiza tus datos personales
                        </p>

                        {success && (
                            <div className="alert alert--success">
                                Perfil actualizado correctamente.
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="profile-card__form">
                            <div className="form-group">
                                <label htmlFor="nickname" className="form-label">
                                    Apodo
                                </label>
                                <input
                                    id="nickname"
                                    type="text"
                                    name="nickname"
                                    value={values.nickname}
                                    onChange={handleChange}
                                    className={`form-input ${errors.nickname ? 'form-input--error' : ''}`}
                                    required
                                />
                                {errors.nickname && (
                                    <p className="form-error">{errors.nickname}</p>
                                )}
                            </div>

                            <div className="form-group">
                                <label htmlFor="email" className="form-label">
                                    Correo electrónico
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={values.email}
                                    onChange={handleChange}
                                    className={`form-input ${errors.email ? 'form-input--error' : ''}`}
                                    required
                                />
                                {errors.email && (
                                    <p className="form-error">{errors.email}</p>
                                )}
                            </div>

                            <hr className="profile-card__divider" />

                            <h2 className="profile-card__section-title">Cambiar contraseña</h2>
                            <p className="profile-card__section-desc">
                                Deja en blanco si no deseas cambiarla.
                            </p>

                            <div className="form-group">
                                <label htmlFor="current_password" className="form-label">
                                    Contraseña actual
                                </label>
                                <input
                                    id="current_password"
                                    type="password"
                                    name="current_password"
                                    value={values.current_password}
                                    onChange={handleChange}
                                    className={`form-input ${errors.current_password ? 'form-input--error' : ''}`}
                                    autoComplete="current-password"
                                />
                                {errors.current_password && (
                                    <p className="form-error">{errors.current_password}</p>
                                )}
                            </div>

                            <div className="form-group">
                                <label htmlFor="password" className="form-label">
                                    Nueva contraseña
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={values.password}
                                    onChange={handleChange}
                                    className={`form-input ${errors.password ? 'form-input--error' : ''}`}
                                    autoComplete="new-password"
                                    minLength={8}
                                />
                                <p className="form-hint">
                                    Mínimo 8 caracteres, mayúsculas, minúsculas y números.
                                </p>
                                {errors.password && (
                                    <p className="form-error">{errors.password}</p>
                                )}
                            </div>

                            <div className="form-group">
                                <label htmlFor="password_confirmation" className="form-label">
                                    Confirmar nueva contraseña
                                </label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    value={values.password_confirmation}
                                    onChange={handleChange}
                                    className={`form-input ${errors.password_confirmation ? 'form-input--error' : ''}`}
                                    autoComplete="new-password"
                                />
                                {errors.password_confirmation && (
                                    <p className="form-error">{errors.password_confirmation}</p>
                                )}
                            </div>

                            <div className="profile-card__actions">
                                <button
                                    type="submit"
                                    className="btn btn--primary"
                                    disabled={processing}
                                >
                                    {processing ? 'Guardando...' : 'Guardar cambios'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Layout>
        </>
    );
}
