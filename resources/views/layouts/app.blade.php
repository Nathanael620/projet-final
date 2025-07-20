<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Soutiens-moi!') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Bootstrap & App CSS/JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @include('layouts.navigation')

        <!-- Dark mode style -->
        <style>
        body.dark-mode, html.dark-mode {
            background-color: #181a1b !important;
            color: #f3f4f6 !important;
        }
        body.dark-mode .navbar, body.dark-mode .card, body.dark-mode .modal-content, body.dark-mode .dropdown-menu {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
        }
        body.dark-mode .bg-white, body.dark-mode .bg-light {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
        }
        body.dark-mode .border-bottom, body.dark-mode .border, body.dark-mode .shadow-sm {
            border-color: #333 !important;
        }
        body.dark-mode .form-control, body.dark-mode .form-select {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
            border-color: #444 !important;
        }
        body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
            background-color: #23272a !important;
            color: #fff !important;
            border-color: #2563eb !important;
        }
        body.dark-mode .form-control::placeholder {
            color: #b0b3b8 !important;
            opacity: 1;
        }
        body.dark-mode .btn-primary, body.dark-mode .btn-outline-primary {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }
        body.dark-mode .btn-outline-secondary {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
            border-color: #444 !important;
        }
        body.dark-mode .btn, body.dark-mode .btn:focus, body.dark-mode .btn:active {
            color: #fff !important;
        }
        body.dark-mode .dropdown-menu {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
        }
        body.dark-mode .dropdown-item {
            color: #f3f4f6 !important;
        }
        body.dark-mode .dropdown-item.active, body.dark-mode .dropdown-item:active {
            background-color: #2563eb !important;
            color: #fff !important;
        }
        body.dark-mode .alert {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
            border-color: #444 !important;
        }
        body.dark-mode .alert-success {
            background-color: #1e4620 !important;
            color: #d1fae5 !important;
            border-color: #256029 !important;
        }
        body.dark-mode .alert-danger {
            background-color: #4b1e1e !important;
            color: #fee2e2 !important;
            border-color: #7f1d1d !important;
        }
        body.dark-mode .alert-info {
            background-color: #1e3a5c !important;
            color: #dbeafe !important;
            border-color: #2563eb !important;
        }
        body.dark-mode .table {
            color: #f3f4f6 !important;
        }
        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #23272a !important;
        }
        body.dark-mode .modal-content {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
        }
        body.dark-mode .badge.bg-primary, body.dark-mode .badge.bg-success {
            color: #fff !important;
        }
        body.dark-mode .nav-link, body.dark-mode .navbar-brand, body.dark-mode .dropdown-toggle {
            color: #f3f4f6 !important;
        }
        body.dark-mode .nav-link.active, body.dark-mode .nav-link:focus, body.dark-mode .nav-link:hover {
            color: #60a5fa !important;
        }
        body.dark-mode a, body.dark-mode a:visited {
            color: #60a5fa !important;
        }
        body.dark-mode a:hover {
            color: #93c5fd !important;
        }
        body.dark-mode .input-group-text {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
            border-color: #444 !important;
        }
        body.dark-mode .form-check-input:checked {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        body.dark-mode .form-check-label {
            color: #f3f4f6 !important;
        }
        body.dark-mode .list-group-item {
            background-color: #23272a !important;
            color: #f3f4f6 !important;
            border-color: #444 !important;
        }
        body.dark-mode .list-group-item.active {
            background-color: #2563eb !important;
            color: #fff !important;
            border-color: #2563eb !important;
        }
        body.dark-mode .progress-bar {
            background-color: #2563eb !important;
        }
        body.dark-mode .progress {
            background-color: #23272a !important;
        }
        body.dark-mode .text-muted {
            color: #b0b3b8 !important;
        }
        body.dark-mode .bg-primary {
            background-color: #2563eb !important;
        }
        body.dark-mode .bg-success {
            background-color: #059669 !important;
        }
        body.dark-mode .bg-danger {
            background-color: #dc2626 !important;
        }
        body.dark-mode .bg-warning {
            background-color: #f59e42 !important;
        }
        body.dark-mode .bg-info {
            background-color: #2563eb !important;
        }
        </style>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm mb-4">
                <div class="container py-4">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
        
        <!-- Scripts -->
        @stack('scripts')

        <!-- Push Notifications -->
        <script src="{{ asset('js/push-notifications.js') }}"></script>

        <!-- Dark mode script placé à la fin du body pour garantir la présence du bouton -->
        <script>
        (function() {
            function setDarkMode(enabled) {
                if (enabled) {
                    document.body.classList.add('dark-mode');
                    document.documentElement.classList.add('dark-mode');
                    localStorage.setItem('darkMode', '1');
                    // Change icon to sun
                    var icon = document.querySelector('#darkModeToggle i');
                    if (icon) {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    }
                } else {
                    document.body.classList.remove('dark-mode');
                    document.documentElement.classList.remove('dark-mode');
                    localStorage.setItem('darkMode', '0');
                    // Change icon to moon
                    var icon = document.querySelector('#darkModeToggle i');
                    if (icon) {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    }
                }
            }
            function initDarkModeBtn() {
                var darkModeBtn = document.getElementById('darkModeToggle');
                if (darkModeBtn) {
                    darkModeBtn.onclick = function(e) {
                        e.preventDefault();
                        setDarkMode(!document.body.classList.contains('dark-mode'));
                    };
                    // Appliquer le dark mode si activé
                    if (localStorage.getItem('darkMode') === '1') {
                        setDarkMode(true);
                    } else {
                        setDarkMode(false);
                    }
                } else {
                    // Si le bouton n'est pas encore là, réessayer dans 100ms
                    setTimeout(initDarkModeBtn, 100);
                }
            }
            // Toujours initialiser après le DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDarkModeBtn);
            } else {
                initDarkModeBtn();
            }
        })();
        </script>
    </body>
</html>
