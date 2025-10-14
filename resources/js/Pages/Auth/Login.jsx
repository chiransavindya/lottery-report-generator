import { useEffect } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';
import "../../../css/login.css";


export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const handleOnChange = (event) => {
        setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <GuestLayout>
            <Head title="Log in" />
            <section className="container">
                <div className="login-box">
                    <div className="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <h1 className="login-title">Sign in to your account</h1>
                        {status && <div className="mb-4 font-medium text-sm text-green-300">{status}</div>}
                        <form className="space-y-4 md:space-y-6" onSubmit={submit}>
                            <div>
                                <InputLabel htmlFor="email" value="Your email" className="block mb-2 text-sm font-medium text-white" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    className="input-field"
                                    placeholder="name@company.com"
                                    autoComplete="username"
                                    isFocused={true}
                                    onChange={handleOnChange}
                                />
                                <InputError message={errors.email} className="input-error" />
                            </div>

                            <div>
                                <InputLabel htmlFor="password" value="Password" className="block mb-2 text-sm font-medium text-white" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={data.password}
                                    className="input-field"
                                    placeholder="••••••••"
                                    autoComplete="current-password"
                                    onChange={handleOnChange}
                                />
                                <InputError message={errors.password} className="input-error" />
                            </div>

                            <div className="flex items-center justify-between">
                                <label className="flex items-center remember-label">
                                    <Checkbox name="remember" checked={data.remember} onChange={handleOnChange} className="checkbox" />
                                    <span className="ml-2 text-sm">Remember me</span>
                                </label>
                                <br />
                                {canResetPassword && (
                                    <Link
                                        href={route('password.request')}
                                        className="forgot-password"
                                    >
                                        Forgot password?
                                    </Link>
                                )}
                            </div>

                            <br />

                            <PrimaryButton className="submit-button" disabled={processing}>
                                Sign in
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </section>
        </GuestLayout>
    );
}