import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';
import "../../css/login.css";

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.value);
    };

    const submit = (e) => {
        e.preventDefault();

        post(route('password.email'));
    };

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <section className="container">
                <div className="login-box">
                    <div className="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <img src="/dlb.png" alt="DLB" className="login-logo" />
                        <h1 className="login-title">Forgot your password?</h1>
                        <p className="text-sm" style={{ color: '#6b7280' }}>
                            Enter your email address and we will send you a link to reset your password.
                        </p>

                        {status && <div className="mb-2 font-medium text-sm text-green-600">{status}</div>}

                        <form className="space-y-4 md:space-y-6" onSubmit={submit}>
                            <div>
                                <InputLabel htmlFor="email" value="Email" className="block mb-2 text-sm font-medium text-gray-700" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    className="input-field"
                                    isFocused={true}
                                    onChange={onHandleChange}
                                />
                                <InputError message={errors.email} className="input-error" />
                            </div>

                            <PrimaryButton className="submit-button" disabled={processing}>
                                Email Password Reset Link
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </section>
        </GuestLayout>
    );
}
