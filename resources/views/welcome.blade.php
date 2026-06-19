<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>EducPlanner - IUC | Gestion d'Emplois du Temps Intelligents</title>

        <!-- Google Fonts: Inter & Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700|Outfit:400,500,600,700&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Inline Theme Script to prevent flashing -->
        <script>
            window.updateThemeIcon = function(theme) {
                var icons = document.querySelectorAll('.theme-toggle-icon');
                icons.forEach(function(icon) {
                    icon.textContent = theme === 'dark' ? '☀️' : '🌙';
                });
            };

            window.setTheme = function(theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                try {
                    localStorage.setItem('theme', theme);
                } catch (e) {}

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
                } catch (e) {}
                var systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                window.currentTheme = storedTheme || (systemPrefersDark ? 'dark' : 'light');
                window.setTheme(window.currentTheme);
            })();
        </script>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 transition-colors duration-300">
        <!-- Glowing background decorations -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 dark:bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-indigo-400/20 dark:bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Glassmorphic Header -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-white/70 dark:bg-slate-900/70 border-b border-slate-200/50 dark:border-slate-800/50 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Logo & School Brand -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 shadow-md transition-transform hover:scale-[1.05] duration-300 p-1 bg-gradient-to-tr from-white to-slate-50 dark:from-slate-900 dark:to-slate-850">
                        <img src="{{ asset('logo.png') }}" class="w-full h-full object-contain" alt="EducPlanner Logo">
                    </div>
                    <div>
                        <h1 class="text-lg font-bold font-outfit text-slate-900 dark:text-white leading-none">EducPlanner</h1>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-indigo-600 dark:text-indigo-400 mt-1">Plateforme 3IAC</p>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="flex items-center gap-4">
                    <!-- Language Picker -->
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800/80 p-1 rounded-lg text-xs font-semibold uppercase">
                        <a href="{{ route('lang.switch', ['locale' => 'fr']) }}" class="px-2.5 py-1 rounded-md transition-all {{ app()->getLocale() === 'fr' ? 'bg-white text-blue-600 dark:bg-slate-700 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">FR</a>
                        <span class="text-slate-300 dark:text-slate-700 px-0.5">|</span>
                        <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="px-2.5 py-1 rounded-md transition-all {{ app()->getLocale() === 'en' ? 'bg-white text-blue-600 dark:bg-slate-700 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">EN</a>
                    </div>

                    <!-- Theme Toggle -->
                    <button type="button" onclick="toggleTheme()" class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <span class="theme-toggle-icon">🌙</span>
                    </button>

                    <!-- Authentication Link -->
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium text-sm hover:shadow-lg hover:shadow-blue-500/20 hover:scale-[1.02] transition-all">
                            {{ app()->getLocale() === 'fr' ? 'Tableau de bord' : 'Dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white font-medium text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            {{ app()->getLocale() === 'fr' ? 'Se connecter' : 'Log in' }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative pt-12 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Badge -->
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 animate-pulse"></span>
                    {{ app()->getLocale() === 'fr' ? 'Moteur d\'EDT Intelligent V2' : 'Timetable Intelligent Engine V2' }}
                </span>

                <!-- Headline -->
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black font-outfit text-slate-900 dark:text-white leading-tight">
                    @if (app()->getLocale() === 'fr')
                        Générez vos emplois du temps <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">sans effort et sans conflit</span>
                    @else
                        Generate your timetables <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">effortlessly and conflict-free</span>
                    @endif
                </h2>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                    @if (app()->getLocale() === 'fr')
                        Conçu spécifiquement pour l'IUC (3IAC). Un puissant solveur par backtracking assurant une contiguïté horaire absolue, la cohésion par matière de même nom, et un flux collaboratif dynamique.
                    @else
                        Tailored specifically for IUC (3IAC). A powerful backtracking solver ensuring absolute slot contiguity, block cohesion of same-name subjects, and a collaborative dynamic flow.
                    @endif
                </p>

                <!-- Actions -->
                <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-xl shadow-blue-500/20 hover:scale-[1.02] transition-all">
                            {{ app()->getLocale() === 'fr' ? 'Entrer dans l\'application' : 'Access Application' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-xl shadow-blue-500/20 hover:scale-[1.02] transition-all">
                            {{ app()->getLocale() === 'fr' ? 'Se connecter' : 'Sign in' }}
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Premium Dashboard Browser Mockup -->
            <div class="mt-16 rounded-2xl border border-slate-200 dark:border-slate-850 bg-white dark:bg-slate-900 p-2 sm:p-4 shadow-2xl max-w-5xl mx-auto overflow-hidden transition-all duration-500 hover:shadow-indigo-500/10 hover:border-slate-300 dark:hover:border-slate-700/80">
                <!-- Browser Window Container -->
                <div class="rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/60 flex flex-col">
                    <!-- Browser Top Bar -->
                    <div class="flex items-center justify-between px-4 py-3 bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800/60">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-500 block shadow-sm shadow-rose-500/20"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500 block shadow-sm shadow-amber-500/20"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500 block shadow-sm shadow-emerald-500/20"></span>
                        </div>
                        <div class="flex-1 max-w-md mx-auto">
                            <div class="flex items-center justify-center gap-1.5 bg-slate-200 dark:bg-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 font-mono px-3 py-1.5 rounded-lg border border-slate-300/30 dark:border-slate-700/30 select-none">
                                <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd"></path></svg>
                                <span>https://educplanner.iuc.cm/dashboard</span>
                            </div>
                        </div>
                        <div class="hidden sm:flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600 block"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600 block"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600 block"></span>
                        </div>
                    </div>

                    <!-- Inner App Shell Mockup -->
                    <div class="flex flex-1 min-h-[380px] text-left">
                        <!-- Sidebar Mockup -->
                        <div class="hidden md:flex flex-col w-48 bg-slate-100 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800/60 p-4 space-y-4 select-none">
                            <div class="flex items-center gap-2 px-2 py-1">
                                <img src="{{ asset('logo.png') }}" class="w-6 h-6 object-contain" alt="Logo">
                                <span class="text-xs font-bold font-outfit text-slate-900 dark:text-white">EducPlanner</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                                    <span>{{ app()->getLocale() === 'fr' ? 'EDT Générateur' : 'EDT Generator' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-500 dark:text-slate-400 text-xs font-medium hover:bg-slate-200/50 dark:hover:bg-slate-800/40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 01-6 6v-1zm0-10h6v1a6 6 0 01-6-6v-1z"></path></svg>
                                    <span>{{ app()->getLocale() === 'fr' ? 'Enseignants' : 'Teachers' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-500 dark:text-slate-400 text-xs font-medium hover:bg-slate-200/50 dark:hover:bg-slate-800/40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span>{{ app()->getLocale() === 'fr' ? 'Salles & Quotas' : 'Rooms & Quotas' }}</span>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-500 dark:text-slate-400 text-xs font-medium hover:bg-slate-200/50 dark:hover:bg-slate-800/40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span>{{ app()->getLocale() === 'fr' ? 'Sécurité 2FA' : '2FA Security' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Area Mockup -->
                        <div class="flex-1 p-4 sm:p-6 bg-white dark:bg-slate-950 flex flex-col relative overflow-hidden">
                            <!-- Floating Background Watermark Logo -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03] dark:opacity-[0.02]">
                                <img src="{{ asset('logo.png') }}" class="w-72 h-72 object-contain" alt="Watermark logo">
                            </div>

                            <!-- Mockup Subheader -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80 mb-6 z-10">
                                <div>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 font-semibold mb-0.5">
                                        <span>Génie Logiciel</span>
                                        <span>•</span>
                                        <span class="text-indigo-500 uppercase">GLJ2B (Jour)</span>
                                    </div>
                                    <h4 class="text-base sm:text-lg font-bold font-outfit text-slate-900 dark:text-white">{{ app()->getLocale() === 'fr' ? 'Génération de l\'Emploi du Temps' : 'Timetable Drafting & Generation' }}</h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-[10px] font-bold uppercase tracking-wide">
                                        {{ app()->getLocale() === 'fr' ? 'Brouillon' : 'Draft' }}
                                    </div>
                                    <div class="hidden sm:inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs shadow shadow-blue-500/10 gap-1 select-none">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span>{{ app()->getLocale() === 'fr' ? 'Générer' : 'Generate' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mockup Grid Content -->
                            <div class="flex-1 z-10 overflow-x-auto">
                                <div class="min-w-[600px] grid grid-cols-6 gap-2">
                                    <!-- Days column headers -->
                                    <div class="bg-slate-100 dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800/80 rounded-lg p-2 flex flex-col justify-center items-center text-center">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold">Créneaux</span>
                                        <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300 mt-0.5 font-outfit">IUC</span>
                                    </div>
                                    <div class="bg-slate-100 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/60 rounded-lg p-2 text-center">
                                        <span class="text-[9px] uppercase text-slate-400 font-bold">Lundi</span>
                                        <span class="block text-[10px] font-bold text-slate-800 dark:text-slate-200">Monday</span>
                                    </div>
                                    <div class="bg-slate-100 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/60 rounded-lg p-2 text-center">
                                        <span class="text-[9px] uppercase text-slate-400 font-bold">Mardi</span>
                                        <span class="block text-[10px] font-bold text-slate-800 dark:text-slate-200">Tuesday</span>
                                    </div>
                                    <div class="bg-slate-100 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/60 rounded-lg p-2 text-center">
                                        <span class="text-[9px] uppercase text-slate-400 font-bold">Mercredi</span>
                                        <span class="block text-[10px] font-bold text-slate-800 dark:text-slate-200">Wednesday</span>
                                    </div>
                                    <div class="bg-slate-100 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/60 rounded-lg p-2 text-center">
                                        <span class="text-[9px] uppercase text-slate-400 font-bold">Jeudi</span>
                                        <span class="block text-[10px] font-bold text-slate-800 dark:text-slate-200">Thursday</span>
                                    </div>
                                    <div class="bg-slate-100 dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/60 rounded-lg p-2 text-center">
                                        <span class="text-[9px] uppercase text-slate-400 font-bold">Vendredi</span>
                                        <span class="block text-[10px] font-bold text-slate-800 dark:text-slate-200">Friday</span>
                                    </div>

                                    <!-- Row 1: Slot 1 -->
                                    <div class="bg-slate-50 dark:bg-slate-900/40 border border-slate-200/40 dark:border-slate-800/40 rounded-lg p-2 flex flex-col justify-center items-center text-center">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">slot 1</span>
                                        <span class="text-[8px] text-slate-455 mt-0.5">08h - 09h50</span>
                                    </div>
                                    <!-- Mon Slot 1 -->
                                    <div class="bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-[8px] font-bold uppercase mb-1">CM</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Analyse & Algorithmes</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: Dr Nguena</div>
                                    </div>
                                    <!-- Tue Slot 1 -->
                                    <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-[8px] font-bold uppercase mb-1">TD</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Base de Données</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: M. Talla</div>
                                    </div>
                                    <!-- Wed Slot 1 -->
                                    <div class="bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-[8px] font-bold uppercase mb-1">CM</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Génie Logiciel</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: M. Kinfack</div>
                                    </div>
                                    <!-- Thu Slot 1 -->
                                    <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-[8px] font-bold uppercase mb-1">TD</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Analyse & Algorithmes</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: Dr Nguena</div>
                                    </div>
                                    <!-- Fri Slot 1 -->
                                    <div class="bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 text-[8px] font-bold uppercase mb-1">TP</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Programmation Web</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Salle: Labo Info A</div>
                                    </div>

                                    <!-- Row 2: Slot 2 -->
                                    <div class="bg-slate-50 dark:bg-slate-900/40 border border-slate-200/40 dark:border-slate-800/40 rounded-lg p-2 flex flex-col justify-center items-center text-center">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">slot 2</span>
                                        <span class="text-[8px] text-slate-455 mt-0.5">09h55 - 11h45</span>
                                    </div>
                                    <!-- Mon Slot 2 (Cohesion Block: consecutive to Analysis) -->
                                    <div class="bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-[8px] font-bold uppercase mb-1">CM</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Analyse & Algorithmes</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: Dr Nguena</div>
                                    </div>
                                    <!-- Tue Slot 2 (Cohesion Block: consecutive to DB) -->
                                    <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-[8px] font-bold uppercase mb-1">TD</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Base de Données</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: M. Talla</div>
                                    </div>
                                    <!-- Wed Slot 2 (Cohesion Block: consecutive to GL) -->
                                    <div class="bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-[8px] font-bold uppercase mb-1">CM</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Génie Logiciel</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: M. Kinfack</div>
                                    </div>
                                    <!-- Thu Slot 2 (Cohesion Block: consecutive to Analysis) -->
                                    <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-[8px] font-bold uppercase mb-1">TD</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Analyse & Algorithmes</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: Dr Nguena</div>
                                    </div>
                                    <!-- Fri Slot 2 (Cohesion Block: consecutive to Web) -->
                                    <div class="bg-rose-50/70 dark:bg-rose-950/40 border-l-4 border-rose-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 text-[8px] font-bold uppercase mb-1">TP</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Programmation Web</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Salle: Labo Info A</div>
                                    </div>

                                    <!-- Row 3: Slot 3 (Zero gap planning, consecutive spacing) -->
                                    <div class="bg-slate-50 dark:bg-slate-900/40 border border-slate-200/40 dark:border-slate-800/40 rounded-lg p-2 flex flex-col justify-center items-center text-center">
                                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">slot 3</span>
                                        <span class="text-[8px] text-slate-455 mt-0.5">12h - 13h50</span>
                                    </div>
                                    <!-- Mon Slot 3 -->
                                    <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-[8px] font-bold uppercase mb-1">TD</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Maths Discrètes</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: Dr Nguena</div>
                                    </div>
                                    <!-- Tue Slot 3 -->
                                    <div class="bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-[8px] font-bold uppercase mb-1">CM</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Sécurité & Réseau</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Prof: M. Talla</div>
                                    </div>
                                    <!-- Wed Slot 3 (Free Afternoon) -->
                                    <div class="border border-dashed border-slate-200 dark:border-slate-800/80 rounded-lg p-2 flex items-center justify-center text-center">
                                        <span class="text-[8px] text-slate-300 dark:text-slate-700 uppercase font-bold">{{ app()->getLocale() === 'fr' ? 'Libre' : 'Free' }}</span>
                                    </div>
                                    <!-- Thu Slot 3 -->
                                    <div class="border border-dashed border-slate-200 dark:border-slate-800/80 rounded-lg p-2 flex items-center justify-center text-center">
                                        <span class="text-[8px] text-slate-300 dark:text-slate-700 uppercase font-bold">{{ app()->getLocale() === 'fr' ? 'Libre' : 'Free' }}</span>
                                    </div>
                                    <!-- Fri Slot 3 -->
                                    <div class="bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-500 rounded-r-lg p-2 text-left">
                                        <div class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-[8px] font-bold uppercase mb-1">CM</div>
                                        <div class="text-[9px] font-extrabold text-slate-900 dark:text-white truncate">Anglais Commercial</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">Salle: Amphi A</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="py-20 bg-slate-100 dark:bg-slate-900/40 border-y border-slate-200/50 dark:border-slate-800/50 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
                    <h3 class="text-3xl font-bold font-outfit text-slate-950 dark:text-white">
                        {{ app()->getLocale() === 'fr' ? 'Fonctionnalités Clés Académiques' : 'Core Academic Features' }}
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400">
                        {{ app()->getLocale() === 'fr' ? 'Une réponse chirurgicale aux problématiques d\'organisation des campus de l\'IUC.' : 'A surgical solution to the scheduling challenges of IUC campuses.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h4 class="font-bold text-lg font-outfit text-slate-950 dark:text-white mb-2">{{ app()->getLocale() === 'fr' ? 'Solveur Backtracking' : 'Backtracking Solver' }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'fr' ? 'Zéro trou dans la journée de l\'étudiant et regroupement consécutif (back-to-back) des matières de même nom.' : 'Zero gaps in student days and mandatory block-cohesion grouping of identical-name subjects.' }}
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.22"></path></svg>
                        </div>
                        <h4 class="font-bold text-lg font-outfit text-slate-950 dark:text-white mb-2">{{ app()->getLocale() === 'fr' ? 'Remplacement en un clic' : 'Auto-Replacement' }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'fr' ? 'Un enseignant refuse un créneau ? L\'algorithme cherche et propose instantanément des remplaçants qualifiés et disponibles.' : 'A teacher refuses a slot? The engine instantly computes qualified, conflict-free replacements.' }}
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h4 class="font-bold text-lg font-outfit text-slate-950 dark:text-white mb-2">{{ app()->getLocale() === 'fr' ? 'Double Facteur (2FA)' : '2FA Advanced Security' }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'fr' ? 'TOTP classique (Google Authenticator) et OTP par e-mail obligatoires pour sécuriser les comptes d\'administration.' : 'Strict TOTP (Google Authenticator) and E-mail OTP options to secure critical administrator dashboards.' }}
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300">
                        <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="font-bold text-lg font-outfit text-slate-950 dark:text-white mb-2">{{ app()->getLocale() === 'fr' ? 'PDF Bilingue Officiel' : 'Official Bilingual PDF' }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'fr' ? 'Exportation de grilles soignées conformes au format IUC, avec filigrane institutionnel et cartouches de signatures.' : 'Pristine landscape PDF sheets tailored to IUC branding, with official seal and sign-off blocks.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic User Flows -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
                <h3 class="text-3xl font-bold font-outfit text-slate-950 dark:text-white">
                    {{ app()->getLocale() === 'fr' ? 'Un flux unifié pour chaque acteur' : 'A Unified Workflow for All Roles' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'fr' ? 'Découvrez comment les différents profils collaborent au cours d\'une semaine.' : 'Discover how different user roles collaborate dynamically during the week.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Admin Card -->
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ app()->getLocale() === 'fr' ? 'Administrateur' : 'Administration' }}</span>
                        <h4 class="text-xl font-bold font-outfit text-slate-950 dark:text-white mt-4 mb-3">{{ app()->getLocale() === 'fr' ? 'Contrôle & Validation' : 'Control & Validation' }}</h4>
                        <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Génération automatique d\'EDT sans conflits' : 'Conflict-free automatic generation' }}</li>
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Alerte de quotas d\'heures restants faibles' : 'Visual warnings for remaining quotas' }}</li>
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Publication verrouillée et dépublication' : 'Official publication locks & rollback' }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Teacher Card -->
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">{{ app()->getLocale() === 'fr' ? 'Professeur' : 'Teacher' }}</span>
                        <h4 class="text-xl font-bold font-outfit text-slate-950 dark:text-white mt-4 mb-3">{{ app()->getLocale() === 'fr' ? 'Disponibilités & Validation' : 'Availabilities & Validation' }}</h4>
                        <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Déclaration des créneaux libres en semaine' : 'Easy weekly free-slot declarations' }}</li>
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Validation ou refus motivé des propositions' : 'Accept or decline pending class slots' }}</li>
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Visualisation de son planning hebdomadaire' : 'Personal dynamic weekly calendar view' }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Student Card -->
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 uppercase tracking-widest">{{ app()->getLocale() === 'fr' ? 'Étudiant' : 'Student' }}</span>
                        <h4 class="text-xl font-bold font-outfit text-slate-950 dark:text-white mt-4 mb-3">{{ app()->getLocale() === 'fr' ? 'Consultation & Téléchargement' : 'Consult & Download' }}</h4>
                        <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Accès à la grille officielle de sa classe' : 'Access the official class timetable grid' }}</li>
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Code couleur distinctif CM / TD / TP' : 'Color coded classes (Lecture, Lab, Tutorial)' }}</li>
                            <li class="flex items-start gap-2">➔ {{ app()->getLocale() === 'fr' ? 'Téléchargement bilingue en format PDF' : 'Download signed bilingual PDF sheet' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Test Accounts Panel (Premium & Visual) -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 rounded-3xl bg-slate-900 text-white shadow-xl dark:bg-slate-900/60 border border-slate-800">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold font-outfit">{{ app()->getLocale() === 'fr' ? 'Comptes de Test Académiques' : 'Academic Demonstration Credentials' }}</h3>
                        <p class="text-slate-400 text-sm mt-1">
                            {{ app()->getLocale() === 'fr' ? 'Utilisez ces comptes pour explorer le flux complet d\'EducPlanner.' : 'Use these accounts to test the entire lifecycle of the application.' }}
                        </p>
                    </div>
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition shadow-lg shadow-blue-500/20">
                        {{ app()->getLocale() === 'fr' ? 'Accéder à la Page de Connexion' : 'Go to Login Page' }}
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Admin credentials -->
                    <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <h4 class="font-bold text-slate-100 font-outfit">{{ app()->getLocale() === 'fr' ? 'Chef de Département GL' : 'GL Head of Department' }}</h4>
                        </div>
                        <p class="text-xs text-slate-400 font-mono select-all">Email: chefgl@iuc.cm</p>
                        <p class="text-xs text-slate-400 font-mono mt-1 select-all">Password: ChefGLPassword</p>
                    </div>

                    <!-- Teacher credentials -->
                    <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <h4 class="font-bold text-slate-100 font-outfit">{{ app()->getLocale() === 'fr' ? 'Enseignant (Dr Nguena)' : 'Teacher (Dr Nguena)' }}</h4>
                        </div>
                        <p class="text-xs text-slate-400 font-mono select-all">Email: nguena@iuc.cm</p>
                        <p class="text-xs text-slate-400 font-mono mt-1 select-all">Password: TeacherPassword</p>
                    </div>

                    <!-- Student credentials -->
                    <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <h4 class="font-bold text-slate-100 font-outfit">{{ app()->getLocale() === 'fr' ? 'Étudiant (Fouda Christian)' : 'Student (Fouda Christian)' }}</h4>
                        </div>
                        <p class="text-xs text-slate-400 font-mono select-all">Email: student@iuc.cm</p>
                        <p class="text-xs text-slate-400 font-mono mt-1 select-all">Password: StudentPassword</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-slate-200 dark:border-slate-900 bg-white dark:bg-slate-950 transition-colors text-center text-xs text-slate-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
                <p>&copy; {{ date('Y') }} Institut Universitaire de la Côte - 3IAC. Tous droits réservés.</p>
                <p class="text-[10px] text-slate-300 dark:text-slate-800">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </p>
            </div>
        </footer>
    </body>
</html>
