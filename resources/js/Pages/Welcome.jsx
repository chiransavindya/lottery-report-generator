import { Link, Head } from '@inertiajs/react';
import '../../css/welcome.css';

export default function Welcome(props) {
    return (
        <>
            <Head title="Welcome" />
            <div className="relative min-h-screen bg-gradient flex justify-center items-center">
                <div className="link-container">
                    {props.auth.user ? (
                        <Link
                            href={route('dashboard')}
                            className="text-white-links"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <div>
                            <Link
                                href={route('login')}
                                className="text-white-links"
                            >
                                Log in
                            </Link>
                            <span className="mx-4 text-white"> | </span>
                            <Link
                                href={route('register')}
                                className="text-white-links"
                            >
                                Register
                            </Link>
                        </div>
                    )}
                </div>

                <div className="card">
                    <h1>Welcome to Newspaper Advertisement Automation System</h1>

                    {/* Add DLB Logo below the heading */}
                    <img
                        src="./images/logo.png"  // Replace with your logo path
                        alt="Development Lotteries Board Logo"
                        className="logo"
                    />

                    <div className="button-container">
                        {props.auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="button button-primary"
                            >
                                Go to Dashboard
                            </Link>
                        ) : (
                            <div>
                                <Link
                                    href={route('login')}
                                    className="button"
                                >
                                    Log in
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="button"
                                >
                                    Register
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
