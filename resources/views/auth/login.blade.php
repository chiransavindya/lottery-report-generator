<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - LRMS v2</title>
    <link rel="icon" href="{{ asset('images/logo/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            background-color: var(--bg-gray);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .login-container {
            background: var(--dlb-white);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--dlb-red), var(--dlb-orange), var(--dlb-yellow), var(--dlb-green));
            background-size: 300% 100%;
            animation: gradient-shift 6s ease infinite;
        }

        .brand-logo {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 0.95rem;
            color: var(--text-soft);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
            font-family: inherit;
        }

        input:focus {
            border-color: var(--dlb-orange);
            box-shadow: 0 0 0 4px rgba(252, 163, 17, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--dlb-red), var(--dlb-orange));
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            gap: 10px;
        }

        input[type="checkbox"] {
            accent-color: var(--dlb-red);
            width: 16px;
            height: 16px;
        }

        .checkbox-label {
            font-size: 0.9rem;
            color: var(--text-soft);
            font-weight: 500;
            margin-bottom: 0;
            cursor: pointer;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background-color: #ffebee;
            color: var(--dlb-red);
            border: 1px solid rgba(230, 57, 70, 0.2);
        }

        .alert-success {
            background-color: #e0f2f1;
            color: var(--dlb-green);
            border: 1px solid rgba(42, 157, 143, 0.2);
        }

        .footer-copy {
            margin-top: 30px;
            font-size: 0.8rem;
            color: var(--text-soft);
        }
    </style>
</head>

<body>
    <div class="login-container animate-up">
        <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" class="brand-logo">
        <h1>LRMS v2</h1>
        <p class="subtitle">Lottery Report Management System</p>

        @if($errors->any())
            <div class="alert alert-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="example@domain.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password">
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember" class="checkbox-label">Remember me</label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="footer-copy">
            &copy; {{ date('Y') }} LRMS v2. Development Lotteries Board.
        </div>
    </div>
</body>

</html>