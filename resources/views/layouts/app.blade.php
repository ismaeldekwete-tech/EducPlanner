<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            window.updateThemeIcon = function(theme) {
                var icon = document.getElementById('theme-toggle-icon');
                if (!icon) {
                    return;
                }
                icon.textContent = theme === 'dark' ? '☀️' : '🌙';
            };

            window.setTheme = function(theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                try {
                    localStorage.setItem('theme', theme);
                } catch (e) {
                    // ignore storage errors
                }

                window.currentTheme = theme;

                if (document.readyState !== 'loading') {
                    window.updateThemeIcon(theme);
                } else {
                    document.addEventListener('DOMContentLoaded', function() {
                        window.updateThemeIcon(theme);
                    });
                }
            };

            window.toggleTheme = function() {
                window.setTheme(window.currentTheme === 'dark' ? 'light' : 'dark');
            };

            (function() {
                var storedTheme = null;

                try {
                    storedTheme = localStorage.getItem('theme');
                } catch (e) {
                    storedTheme = null;
                }

                var systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                window.currentTheme = storedTheme || (systemPrefersDark ? 'dark' : 'light');
                window.setTheme(window.currentTheme);
            })();
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>
