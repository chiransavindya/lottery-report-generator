<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @viteReactRefresh
    @vite(['src/app.jsx'])
    @inertiaHead

    <!-- Force reload on navigation to fix bold text/FOUT issue -->
    <script>
        // Track page URL to detect Inertia navigation
        (function () {
            let lastUrl = window.location.href;
            let pageLoadCount = parseInt(sessionStorage.getItem('pageLoadCount') || '0');

            // For first-time hard reload (page refresh)
            if (pageLoadCount === 0) {
                sessionStorage.setItem('pageLoadCount', '1');
                setTimeout(() => {
                    window.location.reload();
                }, 100);
                return;
            }

            // Listen for Inertia page changes
            document.addEventListener('inertia:navigate', function (event) {
                const currentUrl = window.location.href;
                if (currentUrl !== lastUrl) {
                    lastUrl = currentUrl;
                    // Force hard reload after Inertia navigation
                    setTimeout(() => {
                        window.location.reload();
                    }, 100);
                }
            });

            // Also listen for before Inertia loads
            document.addEventListener('inertia:start', function (event) {
                // Mark that we're navigating
                sessionStorage.setItem('inertiaNavigating', 'true');
            });

            // After successful Inertia load
            document.addEventListener('inertia:success', function (event) {
                if (sessionStorage.getItem('inertiaNavigating') === 'true') {
                    sessionStorage.removeItem('inertiaNavigating');
                    // Force page reload after Inertia completes
                    setTimeout(() => {
                        window.location.reload();
                    }, 100);
                }
            });
        })();
    </script>
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>