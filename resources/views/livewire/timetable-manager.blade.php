    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Panel with IUC Branding and Language Switches -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 transition-all duration-300">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 dark:bg-indigo-500 rounded-xl flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-indigo-100 dark:shadow-none">
                        IUC
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ __('EducPlanner : Gestion Scolaire & Emplois de Temps') }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Institut Universitaire de la Côte - Département 3IAC') }}</p>
                    </div>
                </div>

                <!-- Status & Stats Brief if active -->
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('Semaine sélectionnée :') }} <strong class="text-slate-700 dark:text-slate-300">{{ $selectedWeekKey ? explode('-', $selectedWeekKey)[1] : date('W') }}</strong></span>
                    <span class="h-4 w-px bg-slate-200 dark:bg-slate-700"></span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('Année Académique :') }} <strong class="text-slate-700 dark:text-slate-300">{{ $selectedWeekKey ? explode('-', $selectedWeekKey)[0] : date('Y') }}</strong></span>
                </div>
            </div>

            <!-- Global Toast Messages -->
            @if($successMsg)
                <div class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50 rounded-xl flex items-center space-x-2 animate-fade-in">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ $successMsg }}</span>
                    <button wire:click="$set('successMsg', '')" class="ml-auto text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            @endif

            @if($errorMsg)
                <div class="mt-4 p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 border border-rose-100 dark:border-rose-900/50 rounded-xl flex items-center space-x-2 animate-fade-in">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span class="text-sm font-medium">{{ $errorMsg }}</span>
                    <button wire:click="$set('errorMsg', '')" class="ml-auto text-rose-500 hover:text-rose-700 dark:hover:text-rose-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            @endif
        </div>

        <!-- Navigation Tabs -->
        <div class="flex overflow-x-auto bg-white dark:bg-slate-900 p-1.5 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm scrollbar-none">
            <div class="flex space-x-1 w-full min-w-max">
                @foreach([
                    'grid' => ['label' => __('Grille EDT'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />'],
                    'teachers' => ['label' => __('Enseignants'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'],
                    'rooms' => ['label' => __('Salles de cours'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'],
                    'classes' => ['label' => __('Classes'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />'],
                    'subjects' => ['label' => __('Matières'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'],
                    'assignments' => ['label' => __('Affectations'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />'],
                    'audit_logs' => ['label' => __('Logs d\'audit'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />']
                ] as $tabKey => $tabInfo)
                    <button wire:click="$set('activeTab', '{{ $tabKey }}')"
                            class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeTab === $tabKey ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $tabInfo['icon'] !!}
                        </svg>
                        <span>{{ $tabInfo['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="transition-all duration-300">
            @if($activeTab === 'grid')
                <!-- TIMETABLE GRID VIEW -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <!-- Controls Bar -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="w-full lg:w-1/3">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Classe Cible') }}</label>
                            <select wire:model.live="selectedClasseId" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all">
                                <option value="">{{ __('Sélectionnez une classe') }}</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->code_unique }} ({{ $classe->filiere }} - {{ $classe->regime === 'J' ? __('Jour') : __('Soir') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full lg:w-1/4">
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Semaine cible') }}</label>
                            <select wire:model="selectedWeekKey" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all">
                                @foreach($availableWeeks as $weekKey => $weekLabel)
                                    <option value="{{ $weekKey }}">{{ $weekLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Controls -->
                        @if($selectedClasseId)
                            <div class="flex flex-wrap items-center gap-2">
                                @if(!$currentTimetable || $currentTimetable->status !== 'publie')
                                    <button wire:click="generate"
                                            class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                        <span>{{ __('Générer EDT Intelligent') }}</span>
                                    </button>
                                @endif

                                @if($currentTimetable)
                                    @if($currentTimetable->status !== 'publie')
                                        <button wire:click="sendRequests"
                                                class="flex items-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-4a2 2 0 011.78 0l8 4A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.25 0l-2.25 1.5" /></svg>
                                            <span>{{ __('Envoyer aux Profs') }}</span>
                                        </button>

                                        <button wire:click="publish"
                                                class="flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                            <span>{{ __('Publier l\'Officiel') }}</span>
                                        </button>
                                    @else
                                        <button wire:click="depublish" onclick="return confirm('Voulez-vous vraiment dépublier cet emploi du temps ? Les quotas de toutes les matières programmées seront ré-incrémentés.')"
                                                class="flex items-center space-x-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>{{ __('Dépublier l\'EDT') }}</span>
                                        </button>
                                    @endif

                                    <!-- PDF Export Button -->
                                    <a href="{{ route('timetable.print', $selectedClasseId) }}" target="_blank"
                                       class="flex items-center space-x-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span>{{ __('Exporter PDF') }}</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!$selectedClasseId)
                        <div class="text-center py-20 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                            <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-slate-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">{{ __('Aucune classe sélectionnée') }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Veuillez sélectionner une classe ci-dessus pour gérer son emploi du temps.') }}</p>
                        </div>
                    @else
                        <!-- Low Quotas warning panel -->
                        @php
                            $lowQuotas = $subjects->filter(function($sub) use ($classAssignments) {
                                return $sub->quota_total_remaining_minutes < 240 && $classAssignments->pluck('subject_id')->contains($sub->id);
                            });
                        @endphp
                        @if($lowQuotas->isNotEmpty())
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 rounded-xl space-y-2 animate-fade-in shadow-sm">
                                <div class="flex items-center space-x-2 font-bold text-sm">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <span>{{ __('Attention : Matières avec Quotas Critiques (< 4h restantes pour ce parcours)') }}</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 text-xs">
                                    @foreach($lowQuotas as $lq)
                                        <div class="flex items-center justify-between p-2 bg-white dark:bg-slate-850 rounded-lg shadow-xs border border-amber-100 dark:border-amber-900/30">
                                            <span class="font-semibold truncate pr-2" title="{{ $lq->name }}">{{ $lq->name }}</span>
                                            <span class="font-mono bg-amber-100 text-amber-900 dark:bg-amber-900/50 dark:text-amber-200 px-1.5 py-0.5 rounded text-[10px]">{{ round($lq->quota_total_remaining_minutes / 60, 1) }}h</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Timetable Information bar -->
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-800 text-xs sm:text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-slate-500 dark:text-slate-400">{{ __('Statut :') }}</span>
                                @if($currentTimetable)
                                    @if($currentTimetable->status === 'publie')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            {{ __('Publié & Officiel') }}
                                        </span>
                                    @elseif($currentTimetable->status === 'en_attente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                            {{ __('En attente de validation') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300">
                                            {{ __('Brouillon (Modifiable)') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">{{ __('Non initialisé') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center space-x-1.5"><span class="w-3 h-3 bg-indigo-500 rounded-full"></span><span class="text-slate-500 dark:text-slate-400">CM</span></span>
                                <span class="inline-flex items-center space-x-1.5"><span class="w-3 h-3 bg-emerald-500 rounded-full"></span><span class="text-slate-500 dark:text-slate-400">TD</span></span>
                                <span class="inline-flex items-center space-x-1.5"><span class="w-3 h-3 bg-rose-500 rounded-full"></span><span class="text-slate-500 dark:text-slate-400">TP</span></span>
                            </div>
                        </div>

                        <!-- Grid Table -->
                        <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                            <table class="w-full border-collapse text-left text-xs sm:text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                        <th class="p-4 font-semibold uppercase tracking-wider text-center w-28">{{ __('Jours') }}</th>
                                        @foreach($slots as $slot)
                                            <th class="p-4 font-semibold uppercase tracking-wider text-center min-w-[160px]">{{ $slot['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($days as $day)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                                            <td class="p-4 font-bold text-slate-700 dark:text-slate-300 text-center bg-slate-50/30 dark:bg-slate-800/5">{{ __($day) }}</td>
                                            @foreach($slots as $slot)
                                                @php
                                                    $entry = collect($entries)->first(function ($e) use ($day, $slot) {
                                                        return strtolower($e['day_of_week']) === strtolower($day) && $e['slot_number'] === $slot['label'];
                                                    });
                                                @endphp
                                                <td class="p-2 align-middle text-center relative group min-h-[90px]">
                                                    @if($entry)
                                                        @php
                                                            $type = $entry['subject_teacher']['type'] ?? 'CM';
                                                            $colorClass = $type === 'CM' ? 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-800 dark:text-indigo-300 border-indigo-200 dark:border-indigo-900/60' : ($type === 'TD' ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/60' : 'bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-900/60');
                                                            $badgeClass = $type === 'CM' ? 'bg-indigo-200 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : ($type === 'TD' ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-rose-200 text-rose-800 dark:bg-rose-900 dark:text-rose-200');
                                                            $refuseClass = $entry['teacher_status'] === 'refuse' ? 'ring-2 ring-rose-500 dark:ring-rose-500/80 shadow-rose-100 dark:shadow-none border-rose-500 dark:border-rose-500' : '';
                                                        @endphp

                                                        @if($currentTimetable && $currentTimetable->status === 'publie')
                                                            <!-- Verrouillé car publié -->
                                                            <div class="p-3 border rounded-xl shadow-sm text-left relative overflow-hidden {{ $colorClass }} {{ $refuseClass }}">
                                                                <div class="flex items-center justify-between mb-1.5">
                                                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $badgeClass }}">{{ $type }}</span>

                                                                    <!-- Inner validation status indicator -->
                                                                    @if($entry['teacher_status'] === 'confirme')
                                                                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full" title="{{ __('Confirmé par l\'enseignant') }}"></span>
                                                                    @elseif($entry['teacher_status'] === 'refuse')
                                                                        <div class="relative flex h-2.5 w-2.5">
                                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500" title="{{ __('Refusé : ') . ($entry['rejection_reason'] ?? '') }}"></span>
                                                                        </div>
                                                                    @else
                                                                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full" title="{{ __('En attente de réponse') }}"></span>
                                                                    @endif
                                                                </div>

                                                                <div class="font-bold text-xs line-clamp-2 leading-snug mb-1">{{ $entry['subject_teacher']['subject']['name'] ?? 'N/A' }}</div>
                                                                <div class="text-[10px] opacity-80 font-medium truncate mb-1">👨‍🏫 {{ $entry['subject_teacher']['teacher']['name'] ?? '---' }}</div>
                                                                <div class="text-[9px] opacity-75 font-semibold">📍 {{ $entry['room']['name'] ?? 'N/A' }}</div>

                                                                @if($entry['teacher_status'] === 'refuse')
                                                                    <div class="mt-1 text-[9px] text-rose-600 dark:text-rose-400 font-bold bg-rose-50/80 dark:bg-rose-950/40 px-1 py-0.5 rounded border border-rose-100 dark:border-rose-900/30 truncate" title="{{ $entry['rejection_reason'] ?? __('Refusé') }}">
                                                                        ⚠️ {{ $entry['rejection_reason'] ?? __('Refusé') }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div wire:click="openCellEdit('{{ $day }}', '{{ $slot['label'] }}')"
                                                                 class="p-3 border rounded-xl shadow-sm text-left relative overflow-hidden group cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow {{ $colorClass }} {{ $refuseClass }}">

                                                                <div class="flex items-center justify-between mb-1.5">
                                                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $badgeClass }}">{{ $type }}</span>

                                                                    <!-- Inner validation status indicator -->
                                                                    @if($entry['teacher_status'] === 'confirme')
                                                                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full" title="{{ __('Confirmé par l\'enseignant') }}"></span>
                                                                    @elseif($entry['teacher_status'] === 'refuse')
                                                                        <div class="relative flex h-2.5 w-2.5">
                                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500" title="{{ __('Refusé : ') . ($entry['rejection_reason'] ?? '') }}"></span>
                                                                        </div>
                                                                    @else
                                                                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full" title="{{ __('En attente de réponse') }}"></span>
                                                                    @endif
                                                                </div>

                                                                <div class="font-bold text-xs line-clamp-2 leading-snug mb-1">{{ $entry['subject_teacher']['subject']['name'] ?? 'N/A' }}</div>
                                                                <div class="text-[10px] opacity-80 font-medium truncate mb-1">👨‍🏫 {{ $entry['subject_teacher']['teacher']['name'] ?? '---' }}</div>
                                                                <div class="text-[9px] opacity-75 font-semibold">📍 {{ $entry['room']['name'] ?? 'N/A' }}</div>

                                                                @if($entry['teacher_status'] === 'refuse')
                                                                    <div class="mt-1 text-[9px] text-rose-600 dark:text-rose-400 font-bold bg-rose-50/80 dark:bg-rose-950/40 px-1 py-0.5 rounded border border-rose-100 dark:border-rose-900/30 truncate" title="{{ $entry['rejection_reason'] ?? __('Refusé') }}">
                                                                        ⚠️ {{ $entry['rejection_reason'] ?? __('Refusé') }}
                                                                    </div>
                                                                @endif

                                                                <!-- Overlay edit prompt on hover -->
                                                                <div class="absolute inset-0 bg-slate-900/5 dark:bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                                    <svg class="w-5 h-5 text-current opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        @if($currentTimetable && $currentTimetable->status === 'publie')
                                                            <!-- Empty cell locked -->
                                                            <div class="h-16 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center opacity-50 bg-slate-50/50 dark:bg-slate-900/30">
                                                                <span class="text-xs font-semibold text-slate-400 dark:text-slate-600 flex items-center space-x-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                                                    <span>{{ __('Verrouillé') }}</span>
                                                                </span>
                                                            </div>
                                                        @else
                                                            <!-- Empty slot with placeholder trigger -->
                                                            <div wire:click="openCellEdit('{{ $day }}', '{{ $slot['label'] }}')"
                                                                 class="h-16 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-700 transition-all group">
                                                                <span class="text-xs font-semibold text-slate-400 dark:text-slate-600 group-hover:text-slate-600 dark:group-hover:text-slate-400 flex items-center space-x-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                                    <span>{{ __('Ajouter') }}</span>
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            @if($activeTab === 'teachers')
                <!-- CRUD TEACHERS TAB -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Gestion des Enseignants') }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Ajouter, modifier ou configurer les professeurs et leurs disponibilités générales.') }}</p>
                        </div>
                        <button wire:click="openTeacherCreate" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Nouvel Enseignant') }}</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 font-semibold">
                                    <th class="p-4">{{ __('Nom complet') }}</th>
                                    <th class="p-4">{{ __('E-mail') }}</th>
                                    <th class="p-4">{{ __('Téléphone') }}</th>
                                    <th class="p-4 text-center">{{ __('Dispos Générales') }}</th>
                                    <th class="p-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($teachers as $teacher)
                                    <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/10">
                                        <td class="p-4 font-medium text-slate-800 dark:text-slate-200">{{ $teacher->name }}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-400">{{ $teacher->email }}</td>
                                        <td class="p-4 text-slate-500 dark:text-slate-400">{{ $teacher->phone ?? '---' }}</td>
                                        <td class="p-4 text-center">
                                            <button wire:click="manageAvailabilities('{{ $teacher->id }}')"
                                                    class="inline-flex items-center space-x-1 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold text-xs border border-indigo-200 dark:border-indigo-900/50 bg-indigo-50/30 dark:bg-indigo-950/20 px-2.5 py-1 rounded-lg">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                <span>{{ __('Paramétrer') }} ({{ $teacher->availabilities()->count() }})</span>
                                            </button>
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <button wire:click="editTeacher('{{ $teacher->id }}')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-xs">{{ __('Modifier') }}</button>
                                            <button wire:click="deleteTeacher('{{ $teacher->id }}')" onclick="return confirm('Voulez-vous vraiment supprimer cet enseignant ?')" class="text-rose-600 hover:text-rose-800 font-semibold text-xs ml-2">{{ __('Supprimer') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($activeTab === 'rooms')
                <!-- CRUD ROOMS TAB -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Gestion des Salles de Cours') }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Créer et configurer des salles de cours ou laboratoires de TP.') }}</p>
                        </div>
                        <button wire:click="openRoomCreate" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Nouvelle Salle') }}</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($rooms as $room)
                            <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-xl p-5 relative group shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-bold text-slate-800 dark:text-white text-base">{{ $room->name }}</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">👥 {{ __('Capacité :') }} {{ $room->capacity ?? __('Non définie') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $room->is_labo ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300' : 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-300' }}">
                                        {{ $room->is_labo ? __('Labo / TP') : __('Standard') }}
                                    </span>
                                </div>
                                <div class="mt-4 flex items-center justify-end space-x-2 pt-3 border-t border-slate-200/50 dark:border-slate-700/50">
                                    <button wire:click="editRoom('{{ $room->id }}')" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Modifier') }}</button>
                                    <button wire:click="deleteRoom('{{ $room->id }}')" onclick="return confirm('Voulez-vous vraiment supprimer cette salle ?')" class="text-xs font-semibold text-rose-600 hover:underline ml-3">{{ __('Supprimer') }}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($activeTab === 'classes')
                <!-- CRUD CLASSES TAB -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Gestion des Classes & Groupes') }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Gérer les filières académiques de l\'IUC et définir leurs salles attitrées.') }}</p>
                        </div>
                        <button wire:click="openClassCreate" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Nouvelle Classe') }}</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 font-semibold">
                                    <th class="p-4">{{ __('Code de classe') }}</th>
                                    <th class="p-4">{{ __('Filière') }}</th>
                                    <th class="p-4">{{ __('Niveau & Groupe') }}</th>
                                    <th class="p-4">{{ __('Régime') }}</th>
                                    <th class="p-4">{{ __('Salle Attitrée') }}</th>
                                    <th class="p-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($classes as $class)
                                    <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/10">
                                        <td class="p-4 font-bold text-slate-800 dark:text-white">{{ $class->code_unique }}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-400">{{ $class->filiere }}</td>
                                        <td class="p-4 text-slate-500 dark:text-slate-400">Niveau {{ $class->niveau }} - Gp {{ $class->groupe }}</td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $class->regime === 'J' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-slate-700 text-slate-200' }}">
                                                {{ $class->regime === 'J' ? __('Jour') : __('Soir') }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-500 dark:text-slate-400 font-semibold">{{ $class->room->name ?? '---' }}</td>
                                        <td class="p-4 text-right space-x-2">
                                            <button wire:click="editClass('{{ $class->id }}')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-xs">{{ __('Modifier') }}</button>
                                            <button wire:click="deleteClass('{{ $class->id }}')" onclick="return confirm('Voulez-vous vraiment supprimer cette classe ?')" class="text-rose-600 hover:text-rose-800 font-semibold text-xs ml-2">{{ __('Supprimer') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($activeTab === 'subjects')
                <!-- CRUD SUBJECTS TAB -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Gestion des Matières & Quotas') }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Mettre en place les programmes, semestres et quotas d\'heures (CM, TD, TP).') }}</p>
                        </div>
                        <button wire:click="openSubjectCreate" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Nouvelle Matière') }}</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 font-semibold">
                                    <th class="p-4">{{ __('Matière') }}</th>
                                    <th class="p-4">{{ __('Code') }}</th>
                                    <th class="p-4 text-center">{{ __('Semestre') }}</th>
                                    <th class="p-4 text-center">CM / TD / TP</th>
                                    <th class="p-4 text-center">{{ __('Quota Restant (Heures)') }}</th>
                                    <th class="p-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($subjects as $sub)
                                    <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/10">
                                        <td class="p-4 font-bold text-slate-800 dark:text-white">{{ $sub->name }}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-400 font-mono">{{ $sub->code }}</td>
                                        <td class="p-4 text-center text-slate-500 dark:text-slate-400">S{{ $sub->semester }}</td>
                                        <td class="p-4 text-center space-x-1">
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 rounded">{{ round($sub->quota_cm_minutes / 60, 1) }}h</span>
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded">{{ round($sub->quota_td_minutes / 60, 1) }}h</span>
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded">{{ round($sub->quota_tp_minutes / 60, 1) }}h</span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="font-bold {{ $sub->quota_total_remaining_minutes < 110 ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">
                                                {{ round($sub->quota_total_remaining_minutes / 60, 1) }}h
                                            </span>
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <button wire:click="editSubject('{{ $sub->id }}')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-xs">{{ __('Modifier') }}</button>
                                            <button wire:click="deleteSubject('{{ $sub->id }}')" onclick="return confirm('Voulez-vous vraiment supprimer cette matière ?')" class="text-rose-600 hover:text-rose-800 font-semibold text-xs ml-2">{{ __('Supprimer') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($activeTab === 'assignments')
                <!-- CRUD ASSIGNMENTS TAB -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Affectations Enseignants-Matières') }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Assigner les professeurs aux cours (CM, TD ou TP) pour chaque classe.') }}</p>
                        </div>
                        <button wire:click="openAsmCreate" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Nouvelle Affectation') }}</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 font-semibold">
                                    <th class="p-4">{{ __('Classe') }}</th>
                                    <th class="p-4">{{ __('Matière') }}</th>
                                    <th class="p-4">{{ __('Professeur') }}</th>
                                    <th class="p-4 text-center">{{ __('Type de cours') }}</th>
                                    <th class="p-4 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($assignments as $asm)
                                    <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/10">
                                        <td class="p-4 font-bold text-slate-800 dark:text-white">{{ $asm->classe->code_unique ?? '---' }}</td>
                                        <td class="p-4 font-medium text-slate-700 dark:text-slate-300">{{ $asm->subject->name ?? '---' }}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-400">👨‍🏫 {{ $asm->teacher->name ?? '---' }}</td>
                                        <td class="p-4 text-center">
                                            @php
                                                $badgeStyle = $asm->type === 'CM' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : ($asm->type === 'TD' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300');
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold uppercase {{ $badgeStyle }}">{{ $asm->type }}</span>
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <button wire:click="editAsm('{{ $asm->id }}')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-xs">{{ __('Modifier') }}</button>
                                            <button wire:click="deleteAsm('{{ $asm->id }}')" onclick="return confirm('Voulez-vous vraiment supprimer cette affectation ?')" class="text-rose-600 hover:text-rose-800 font-semibold text-xs ml-2">{{ __('Supprimer') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($activeTab === 'audit_logs')
                <!-- AUDIT LOGS TAB -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Journal d\'Audit des Actions d\'Administration') }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Visualiser en temps réel toutes les actions administratives et de sécurité effectuées sur l\'application.') }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800 font-semibold">
                                    <th class="p-3 w-40">{{ __('Date & Heure') }}</th>
                                    <th class="p-3 w-48">{{ __('Utilisateur') }}</th>
                                    <th class="p-3 w-44">{{ __('Action') }}</th>
                                    <th class="p-3">{{ __('Détails de l\'action') }}</th>
                                    <th class="p-3 w-32">{{ __('Adresse IP') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-slate-600 dark:text-slate-400">
                                @foreach($auditLogs as $log)
                                    <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/10">
                                        <td class="p-3 text-slate-400 font-sans whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="p-3 font-sans text-slate-700 dark:text-slate-300 font-semibold truncate">{{ $log->user->name ?? __('Système') }}</td>
                                        <td class="p-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">{{ $log->action }}</span></td>
                                        <td class="p-3 text-xs truncate max-w-md" title="{{ json_encode($log->details) }}">{{ json_encode($log->details) }}</td>
                                        <td class="p-3 text-slate-400">{{ $log->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $auditLogs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL 1: GRID CELL EDITOR -->
    @if($showGridModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        🗓️ {{ __('Planifier un cours :') }} {{ __($editingDay) }} - {{ $editingSlot }}
                    </h3>
                    <button wire:click="$set('showGridModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    @if($gridErrorMessage)
                        <div class="p-3.5 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 border border-rose-100 dark:border-rose-900/40 rounded-xl text-xs font-semibold flex items-start space-x-2">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <span>{{ $gridErrorMessage }}</span>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Cours & Professeur Affecté') }}</label>
                        <select wire:model.live="selectedAssignmentId" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- {{ __('Libérer le créneau (Vide)') }} --</option>
                            @foreach($classAssignments as $asm)
                                <option value="{{ $asm->id }}">
                                    {{ $asm->subject->name }} ({{ $asm->type }}) - {{ $asm->teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedAssignmentId)
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Salle de cours disponible') }}</label>
                            <select wire:model="selectedRoomId" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="">-- {{ __('Sélectionnez une salle') }} --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->is_labo ? __('Labo TP') : __('Standard') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center space-x-2 text-sm text-slate-600 dark:text-slate-400">
                                <input type="checkbox" wire:model="forceQuota" class="rounded border-slate-350 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                <span>⚠️ {{ __('Forcer le quota d\'heures (Loggé comme forçage)') }}</span>
                            </label>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showGridModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveCell" class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: TEACHER FORM -->
    @if($showTeacherModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        👤 {{ $teacherId ? __('Modifier l\'Enseignant') : __('Créer un Enseignant') }}
                    </h3>
                    <button wire:click="$set('showTeacherModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Nom Complet') }}</label>
                        <input type="text" wire:model="teacherName" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('teacherName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Adresse E-mail') }}</label>
                        <input type="email" wire:model="teacherEmail" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('teacherEmail') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Téléphone') }}</label>
                        <input type="text" wire:model="teacherPhone" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('teacherPhone') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Mot de passe') }}</label>
                        <input type="password" wire:model="teacherPassword" placeholder="{{ $teacherId ? __('Laissez vide pour conserver') : '' }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('teacherPassword') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showTeacherModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveTeacher" class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: TEACHER AVAILABILITIES CHECKLIST -->
    @if($showAvailabilitiesModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        🗓️ {{ __('Disponibilités Générales :') }} {{ $availTeacherName }}
                    </h3>
                    <button wire:click="$set('showAvailabilitiesModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="p-3 font-semibold uppercase tracking-wider text-center w-28">{{ __('Jours') }}</th>
                                @foreach($slots as $slot)
                                    <th class="p-3 font-semibold uppercase tracking-wider text-center text-xs min-w-[100px]">{{ $slot['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $dayKey)
                                <tr>
                                    <td class="p-3 font-bold text-slate-700 dark:text-slate-300 text-center uppercase text-xs">{{ __($dayKey) }}</td>
                                    @foreach($slots as $slot)
                                        <td class="p-2 text-center align-middle">
                                            @php
                                                $slotLabel = $slot['label'];
                                                $isSelected = $availGrid[$dayKey][$slotLabel] ?? false;
                                            @endphp
                                            <button type="button" wire:click="toggleAvail('{{ $dayKey }}', '{{ $slotLabel }}')"
                                                    class="w-8 h-8 rounded-lg border font-bold flex items-center justify-center transition-all duration-200 {{ $isSelected ? 'bg-emerald-500 text-white border-emerald-500 hover:bg-emerald-650' : 'bg-slate-50 dark:bg-slate-850 text-slate-300 dark:text-slate-700 border-slate-200 dark:border-slate-700 hover:border-slate-400' }}">
                                                @if($isSelected)
                                                    ✓
                                                @else
                                                    -
                                                @endif
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showAvailabilitiesModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveAvailabilities" class="px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 4: ROOM FORM -->
    @if($showRoomModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        📍 {{ $roomId ? __('Modifier la Salle') : __('Créer une Salle') }}
                    </h3>
                    <button wire:click="$set('showRoomModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Nom de la salle (ex: AK306)') }}</label>
                        <input type="text" wire:model="roomName" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('roomName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Capacité maximale (places)') }}</label>
                        <input type="number" wire:model="roomCapacity" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('roomCapacity') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center space-x-2 text-sm text-slate-600 dark:text-slate-400">
                            <input type="checkbox" wire:model="roomIsLabo" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>💻 {{ __('C\'est un laboratoire de TP informatique') }}</span>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showRoomModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveRoom" class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 5: CLASS FORM -->
    @if($showClassModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        🏫 {{ $classId ? __('Modifier la Classe') : __('Créer une Classe') }}
                    </h3>
                    <button wire:click="$set('showClassModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Code Unique (ex: GLJ2B)') }}</label>
                        <input type="text" wire:model="classCodeUnique" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('classCodeUnique') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Filière (ex: GL)') }}</label>
                            <input type="text" wire:model="classFiliere" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            @error('classFiliere') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Régime') }}</label>
                            <select wire:model="classRegime" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="J">{{ __('Jour (8h-17h)') }}</option>
                                <option value="S">{{ __('Soir (17h30-21h30)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Niveau') }}</label>
                            <input type="number" wire:model="classNiveau" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Groupe') }}</label>
                            <input type="text" wire:model="classGroupe" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Salle Principale Attitrée') }}</label>
                        <select wire:model="classRoomId" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- {{ __('Aucune salle') }} --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showClassModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveClass" class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 6: SUBJECT FORM -->
    @if($showSubjectModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        📚 {{ $subjectId ? __('Modifier la Matière') : __('Créer une Matière') }}
                    </h3>
                    <button wire:click="$set('showSubjectModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Intitulé de la matière') }}</label>
                        <input type="text" wire:model="subjectName" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('subjectName') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Code Unique') }}</label>
                            <input type="text" wire:model="subjectCode" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            @error('subjectCode') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Semestre académique') }}</label>
                            <select wire:model="subjectSemester" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="1">Semestre 1</option>
                                <option value="2">Semestre 2</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Quotas Horaires d\'Enseignement (en Minutes)') }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <span class="block text-[10px] text-indigo-600 font-bold mb-1">CM (Minutes)</span>
                                <input type="number" wire:model="quotaCm" class="w-full bg-indigo-50/20 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div>
                                <span class="block text-[10px] text-emerald-600 font-bold mb-1">TD (Minutes)</span>
                                <input type="number" wire:model="quotaTd" class="w-full bg-emerald-50/20 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div>
                                <span class="block text-[10px] text-rose-600 font-bold mb-1">TP (Minutes)</span>
                                <input type="number" wire:model="quotaTp" class="w-full bg-rose-50/20 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-rose-500">
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-450 mt-1 block">💡 1 Cours = 110 minutes (1h50). Ex : 10 cours = 1100 minutes.</span>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showSubjectModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveSubject" class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 7: ASSIGNMENT FORM -->
    @if($showAsmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">
                        🤝 {{ $asmId ? __('Modifier l\'Affectation') : __('Créer une Affectation') }}
                    </h3>
                    <button wire:click="$set('showAsmModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Classe Cible') }}</label>
                        <select wire:model="asmClasseId" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- {{ __('Sélectionnez une classe') }} --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->code_unique }}</option>
                            @endforeach
                        </select>
                        @error('asmClasseId') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Matière') }}</label>
                        <select wire:model="asmSubjectId" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- {{ __('Sélectionnez une matière') }} --</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                            @endforeach
                        </select>
                        @error('asmSubjectId') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Enseignant') }}</label>
                        <select wire:model="asmTeacherId" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- {{ __('Sélectionnez un professeur') }} --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('asmTeacherId') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Type d\'Enseignement') }}</label>
                        <select wire:model="asmType" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-855 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="CM">{{ __('CM (Cours Magistral)') }}</option>
                            <option value="TD">{{ __('TD (Travaux Dirigés)') }}</option>
                            <option value="TP">{{ __('TP (Travaux Pratiques)') }}</option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showAsmModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="saveAsm" class="px-4 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all">{{ __('Sauvegarder') }}</button>
                </div>
            </div>
        </div>
    @endif
    </div>

