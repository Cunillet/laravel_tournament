import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Register({ auth, errors: initialErrors = {} }) {
    const [values, setValues] = useState({
        nickname: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [errors, setErrors] = useState(initialErrors);
    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setValues((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        router.post(route('register'), values, {
            onError: (errs) => {
                setErrors(errs);
                setProcessing(false);
            },
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title="Registrarse" />
            <Layout auth={auth}>
                <div className="auth-page">
                    <div className="auth-card">
                        <h1 className="auth-card__title">Crear cuenta</h1>
                        <p className="auth-card__subtitle">
                            Únete a DD_Tournaments
                        </p>

                        <form onSubmit={handleSubmit} className="auth-card__form">
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
                                    autoComplete="username"
                                    autoFocus
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
                                    autoComplete="email"
                                />
                                {errors.email && (
                                    <p className="form-error">{errors.email}</p>
                                )}
                            </div>

                            <div className="form-group">
                                <label htmlFor="password" className="form-label">
                                    Contraseña
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={values.password}
                                    onChange={handleChange}
                                    className={`form-input ${errors.password ? 'form-input--error' : ''}`}
                                    required
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
                                    Confirmar contraseña
                                </label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    value={values.password_confirmation}
                                    onChange={handleChange}
                                    className={`form-input ${errors.password_confirmation ? 'form-input--error' : ''}`}
                                    required
                                    autoComplete="new-password"
                                />
                                {errors.password_confirmation && (
                                    <p className="form-error">{errors.password_confirmation}</p>
                                )}
                            </div>

                            <button
                                type="submit"
                                className="btn btn--primary btn--full"
                                disabled={processing}
                            >
                                {processing ? 'Creando cuenta...' : 'Crear cuenta'}
                            </button>

                            {errors.error && (
                                <p className="form-error form-error--center">{errors.error}</p>
                            )}
                        </form>

                        <p className="auth-card__footer">
                            ¿Ya tienes cuenta?{' '}
                            <Link href={route('login')} className="auth-card__link">
                                Inicia sesión aquí
                            </Link>
                        </p>
                    </div>
                </div>
            </Layout>
        </>
    );
}
