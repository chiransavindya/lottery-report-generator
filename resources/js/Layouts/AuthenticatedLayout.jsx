import { useState } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link } from '@inertiajs/react';
import '../../css/authenticated.css';

export default function Authenticated({ auth, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            {/* Navbar */}
            <nav className="nav-container">
                <div className="nav-content">
                    {/* Left: Logo and Links */}
                    <div className="nav-links">
                        <Link href="/" className="nav-logo">
                            <ApplicationLogo />
                        </Link>
                        <NavLink href={route('dashboard')} active={route().current('dashboard')}>
                            Dashboard
                        </NavLink>
                    </div>

                    {/* Right: User Profile Dropdown */}
                    <div className="hidden sm:flex sm:items-center">
                        <Dropdown>
                            <Dropdown.Trigger>
                                <button type="button" className="dropdown-button">
                                    <span>{auth.user.name}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fillRule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </Dropdown.Trigger>
                            <Dropdown.Content>
                                <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                                <Dropdown.Link href={route('logout')} method="post" as="button">
                                    Log Out
                                </Dropdown.Link>
                            </Dropdown.Content>
                        </Dropdown>
                    </div>

                    {/* Mobile Menu Button */}
                    {/* <div className="sm:hidden">
                        <button
                            onClick={() => setShowingNavigationDropdown(!showingNavigationDropdown)}
                            className="mobile-menu-button"
                        >
                            <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d={
                                        showingNavigationDropdown
                                            ? 'M6 18L18 6M6 6l12 12'
                                            : 'M4 6h16M4 12h16M4 18h16'
                                    }
                                />
                            </svg>
                        </button>
                    </div> */}
                </div>

                {/* Mobile Navigation */}
                <div className={`mobile-nav ${showingNavigationDropdown ? 'active' : ''}`}>
                    <div className="space-y-1 pt-2 pb-3">
                        <ResponsiveNavLink href={route('dashboard')} active={route().current('dashboard')}>
                            Dashboard
                        </ResponsiveNavLink>
                    </div>
                    <div className="border-t border-gray-200 pt-4 pb-1">
                        <div className="px-4">
                            <div className="text-base font-medium text-gray-800">{auth.user.name}</div>
                            <div className="text-sm font-medium text-gray-500">{auth.user.email}</div>
                        </div>
                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink href={route('profile.edit')}>Profile</ResponsiveNavLink>
                            <ResponsiveNavLink method="post" href={route('logout')} as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Header */}
            {header && (
                <header className="header-content">
                    <div>{header}</div>
                </header>
            )}

            {/* Main Content */}
            <main className="flex-grow">{children}</main>
        </div>
    );
}
