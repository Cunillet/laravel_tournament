import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Login({ auth, errors: initialErrors = {} }) {
    const [values, setValues] = useState({
        email: '',
        password: '',
        remember: false,
    });
    const [errors, setErrors] = useState(initialErrors);
    const [processing, setProcessing] = useState(false);

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setValues((prev) => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        router.post(route('login'), values, {
            onError: (errs) => {
                setErrors(errs);
                setProcessing(false);
            },
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <>
            <Head title="Iniciar sesión" />
            <Layout auth={auth}>
                <div className="auth-page">
                    <div className="auth-card">
                        <h1 className="auth-card__title">Iniciar sesión</h1>
                        <p className="auth-card__subtitle">
                            Accede a tu cuenta de DD_Tournaments
                        </p>

                        <form onSubmit={handleSubmit} className="auth-card__form">
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
                                    autoFocus
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
                                    autoComplete="current-password"
                                />
                                {errors.password && (
                                    <p className="form-error">{errors.password}</p>
                                )}
                            </div>

                            <div className="form-group form-group--checkbox">
                                <label className="form-checkbox">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        checked={values.remember}
                                        onChange={handleChange}
                                    />
                                    <span>Recordarme</span>
                                </label>
                            </div>

                            <button
                                type="submit"
                                className="btn btn--primary btn--full"
                                disabled={processing}
                            >
                                {processing ? 'Iniciando sesión...' : 'Iniciar sesión'}
                            </button>

                            {errors.error && (
                                <p className="form-error form-error--center">{errors.error}</p>
                            )}
                        </form>

                        <p className="auth-card__footer">
                            ¿No tienes cuenta?{' '}
                            <Link href={route('register')} className="auth-card__link">
                                Regístrate aquí
                            </Link>
                        </p>
                    </div>
                </div>
            </Layout>
        </>
    );
}
