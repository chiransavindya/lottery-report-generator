import { useEffect } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';
import "../../css/login.css";

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const handleOnChange = (event) => {
        setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value);
    };

    const submit = (e) => {
        e.preventDefault();

        post(route('register'));
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <section className="container">
                <div className="login-box">
                    <div className="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <img src="/dlb.png" alt="DLB" className="login-logo" />
                        <h1 className="login-title">Create your account</h1>

                        <form className="space-y-4 md:space-y-6" onSubmit={submit}>
                            <div>
                                <InputLabel htmlFor="name" value="Name" className="block mb-2 text-sm font-medium text-gray-700" />
                                <TextInput
                                    id="name"
                                    name="name"
                                    value={data.name}
                                    className="input-field"
                                    autoComplete="name"
                                    isFocused={true}
                                    onChange={handleOnChange}
                                    required
                                />
                                <InputError message={errors.name} className="input-error" />
                            </div>

                            <div>
                                <InputLabel htmlFor="email" value="Email" className="block mb-2 text-sm font-medium text-gray-700" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    className="input-field"
                                    autoComplete="username"
                                    onChange={handleOnChange}
                                    required
                                />
                                <InputError message={errors.email} className="input-error" />
                            </div>

                            <div>
                                <InputLabel htmlFor="password" value="Password" className="block mb-2 text-sm font-medium text-gray-700" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={data.password}
                                    className="input-field"
                                    autoComplete="new-password"
                                    onChange={handleOnChange}
                                    required
                                />
                                <InputError message={errors.password} className="input-error" />
                            </div>

                            <div>
                                <InputLabel htmlFor="password_confirmation" value="Confirm Password" className="block mb-2 text-sm font-medium text-gray-700" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    className="input-field"
                                    autoComplete="new-password"
                                    onChange={handleOnChange}
                                    required
                                />
                                <InputError message={errors.password_confirmation} className="input-error" />
                            </div>

                            <div className="flex items-center justify-between">
                                <Link href={route('login')} className="forgot-password">Already registered?</Link>
                            </div>

                            <PrimaryButton className="submit-button" disabled={processing}>
                                Register
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </section>
        </GuestLayout>
    );
}
