<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Voedselbank Samen')</title>
    <meta name="description" content="Voedselbank Samen helpt gezinnen met voedselpakketten, begeleiding en lokale ondersteuning.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body d-flex flex-column min-vh-100">
    <div class="page-glow page-glow-left" aria-hidden="true"></div>
    <div class="page-glow page-glow-right" aria-hidden="true"></div>

    <x-navbar />

    <main class="container py-4 py-lg-5 flex-grow-1">
        @if (session('status'))
            <div class="alert alert-success shadow-sm reveal mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <x-footer />
    @stack('scripts')
</body>
</html>
