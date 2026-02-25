<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LRMS') - Lottery Report Management System</title>
    <link rel="icon" href="{{ asset('images/logo/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <style>
        :root {
            --dlb-red: #e63946;
            --dlb-orange: #fca311;
            --dlb-yellow: #ffb703;
            --dlb-green: #2a9d8f;
            --dlb-white: #ffffff;
            --text-main: #2b2d42;
            --text-soft: #8d99ae;
            --bg-gray: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-gray);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Override */
        .navbar {
            background-color: var(--dlb-white);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0 30px;
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--dlb-red), var(--dlb-orange), var(--dlb-yellow), var(--dlb-green));
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--text-main);
            font-size: 1.3rem;
            letter-spacing: -0.5px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 45px;
            margin-right: 10px;
        }

        .nav-links {
            display: flex;
            gap: 10px;
        }

        .nav-link {
            color: var(--text-soft);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--dlb-red);
            background-color: rgba(230, 57, 70, 0.05);
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
            line-height: 1.2;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.95rem;
            display: block;
        }

        .user-role {
            font-size: 0.8rem;
            background: linear-gradient(135deg, var(--dlb-red), var(--dlb-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-outline {
            border: 1.5px solid #eee;
            background: transparent;
            color: var(--text-main);
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }

        .btn-outline:hover {
            border-color: var(--dlb-red);
            color: var(--dlb-red);
        }

        /* Alerts Override */
        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            padding: 16px 20px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #e0f2f1;
            color: var(--dlb-green);
            border-left: 4px solid var(--dlb-green);
        }

        .alert-error {
            background-color: #ffebee;
            color: var(--dlb-red);
            border-left: 4px solid var(--dlb-red);
        }

        .alert-warning {
            background-color: #fff8e1;
            color: #f57f17;
            border-left: 4px solid var(--dlb-orange);
        }

        /* Footer Override */
        .footer {
            background: transparent;
            border-top: none;
            color: var(--text-soft);
            margin-top: auto;
            padding: 40px 20px;
            text-align: center;
        }

        /* Card Override for Dashboard/Content */
        .card {
            background: var(--dlb-white);
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .container {
            width: 100%;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 15px;
                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .user-menu {
                width: 100%;
                justify-content: space-between;
                padding-top: 15px;
                border-top: 1px solid #eee;
            }

            .user-info {
                text-align: left;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ route('home') }}" class="navbar-brand">
            <img src="{{ asset('images/logo/logo.png') }}" alt="LRMS Logo">
            <!-- <span>LRMS v2</span> -->
        </a>

        @auth
            <div class="nav-links">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

                @if(Auth::user()->isOperator() || Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                    <a href="{{ route('uploads.index') }}"
                        class="nav-link {{ request()->routeIs('uploads.*') ? 'active' : '' }}">Uploads</a>
                    <a href="{{ route('reports.index') }}"
                        class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
                @endif

                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
                @endif

                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            </div>

            <div class="user-menu">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-outline">Logout</button>
                </form>
            </div>
        @endauth
    </nav>

    <!-- Main Content -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warnings'))
            @foreach(session('warnings') as $warning)
                <div class="alert alert-warning">
                    {{ $warning }}
                </div>
            @endforeach
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <div style="width: 100%;">
                    <strong>Please correct the following errors:</strong>
                    <ul style="margin-top: 5px; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ date('Y') }} LRMS v2. Development Lotteries Board.</p>
        <p style="font-size: 0.8rem; margin-top: 8px; opacity: 0.7;">
            System Architecture by Chiran Savindya & Niwantha Sithumal
        </p>
    </footer>

    @stack('scripts')
</body>

</html>