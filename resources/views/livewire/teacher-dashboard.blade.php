<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 transition-all">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-100 dark:shadow-none">
                         
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ __('Espace Enseignant') }} : {{ $teacher->name }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Gérer votre planning hebdomadaire, vos disponibilités et valider les propositions de cours.') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 text-xs sm:text-sm">
                    <span class="text-slate-400 dark:text-slate-500">{{ __('Département :') }} <strong class="text-slate-700 dark:text-slate-300">{{ $teacher->department->name ?? '3IAC' }}</strong></span>
                </div>
            </div>

            <!-- Toast Messages -->
            @if($successMsg)
                <div class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50 rounded-xl flex items-center space-x-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ $successMsg }}</span>
                    <button wire:click="$set('successMsg', '')" class="ml-auto text-emerald-500 hover:text-emerald-700"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            @endif

            @if($errorMsg)
                <div class="mt-4 p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 border border-rose-100 dark:border-rose-900/50 rounded-xl flex items-center space-x-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span class="text-sm font-medium">{{ $errorMsg }}</span>
                    <button wire:click="$set('errorMsg', '')" class="ml-auto text-rose-500 hover:text-rose-700"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            @endif
        </div>

        <!-- Navigation Tabs -->
        <div class="flex overflow-x-auto bg-white dark:bg-slate-900 p-1.5 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="flex space-x-1 w-full min-w-max">
                <button wire:click="$set('activeTab', 'schedule')"
                        class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $activeTab === 'schedule' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span>{{ __('Mon Planning Hebdomadaire') }}</span>
                </button>
                
                <button wire:click="$set('activeTab', 'propositions')"
                        class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-lg transition-all relative {{ $activeTab === 'propositions' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span>{{ __('Propositions en attente') }}</span>
                    @if($pendingPropositions->count() > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">
                            {{ $pendingPropositions->count() }}
                        </span>
                    @endif
                </button>

                <button wire:click="$set('activeTab', 'availabilities')"
                        class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $activeTab === 'availabilities' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    <span>{{ __('Mes Disponibilités Générales') }}</span>
                </button>
            </div>
        </div>

        <!-- Contents -->
        <div>
            @if($activeTab === 'schedule')
                <!-- Timetable Grid -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <div class="space-y-1">
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Votre Emploi du Temps') }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                @if($selectedWeek === 'current')
                                    {{ __('Semaine en cours (Semaine :') }} {{ date('W') }})
                                @else
                                    {{ __('Semaine prochaine (Semaine :') }} {{ date('W', strtotime('+1 week')) }})
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Week Selector Switcher -->
                            <div class="inline-flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200/50 dark:border-slate-700">
                                <button type="button" wire:click="$set('selectedWeek', 'current')" 
                                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $selectedWeek === 'current' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                    📅 {{ __('Semaine en cours') }}
                                </button>
                                <button type="button" wire:click="$set('selectedWeek', 'next')" 
                                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $selectedWeek === 'next' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                    ⏭️ {{ __('Semaine prochaine') }}
                                </button>
                            </div>

                            <!-- Personal Export PDF Button -->
                            <a href="{{ route('teacher.print', ['week' => $selectedWeek]) }}" target="_blank"
                               class="flex items-center space-x-2 bg-slate-900 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span>{{ __('Télécharger en PDF') }}</span>
                            </a>
                        </div>
                    </div>

                    @if($selectedWeek === 'next' && $confirmedEntries->isEmpty())
                        <div class="p-5 bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/40 rounded-2xl flex flex-col sm:flex-row items-center gap-3">
                            <div class="text-3xl">📅</div>
                            <div>
                                <h4 class="font-bold text-sm">{{ __('Emploi du temps de la semaine prochaine non disponible') }}</h4>
                                <p class="text-xs text-amber-700/95 dark:text-amber-400/90 mt-0.5">{{ __('L\'administration n\'a pas encore publié et officialisé l\'emploi du temps pour la semaine prochaine. Veuillez réessayer ultérieurement.') }}</p>
                            </div>
                        </div>
                    @endif

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
                                                $entry = $confirmedEntries->first(function ($e) use ($day, $slot) {
                                                    return strtolower($e->day_of_week) === strtolower($day) && $e->slot_number === $slot['label'];
                                                });
                                            @endphp
                                            <td class="p-2 align-middle text-center min-h-[90px]">
                                                @if($entry)
                                                    @php
                                                        $type = $entry->subjectTeacher->type ?? 'CM';
                                                        $colorClass = $type === 'CM' ? 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-800 dark:text-indigo-300 border-indigo-200 dark:border-indigo-900/60' : ($type === 'TD' ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/60' : 'bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-900/60');
                                                        $badgeClass = $type === 'CM' ? 'bg-indigo-200 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : ($type === 'TD' ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-rose-200 text-rose-800 dark:bg-rose-900 dark:text-rose-200');
                                                    @endphp
                                                    <div class="p-3 border rounded-xl shadow-sm text-left relative overflow-hidden {{ $colorClass }}">
                                                        <div class="flex items-center justify-between mb-1.5">
                                                            <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $badgeClass }}">{{ $type }}</span>
                                                            @if($entry->teacher_status === 'en_attente')
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 uppercase animate-pulse">{{ __('À valider') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="font-bold text-xs line-clamp-2 leading-snug mb-1">{{ $entry->subjectTeacher->subject->name ?? 'N/A' }}</div>
                                                        <div class="text-[10px] opacity-80 font-medium truncate mb-1">🏫 {{ __('Classe :') }} {{ $entry->timetable->classe->code_unique }}</div>
                                                        <div class="text-[9px] opacity-75 font-semibold">   {{ $entry->room->name ?? 'N/A' }}</div>
                                                    </div>
                                                @else
                                                    <div class="h-16 border border-dashed border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-350 dark:text-slate-700 text-xs">
                                                        -
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($activeTab === 'propositions')
                <!-- Pending Propositions -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Propositions de cours de la Semaine') }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Valider ou refuser les cours programmés pour vous par l\'administration de l\'IUC.') }}</p>
                    </div>

                    @if($pendingPropositions->count() === 0)
                        <div class="text-center py-20 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                            <svg class="w-16 h-16 mx-auto text-emerald-300 dark:text-emerald-800 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">{{ __('Aucune proposition en attente') }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Vous êtes à jour ! Toutes vos programmations ont été confirmées ou traitées.') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($pendingPropositions as $prop)
                                <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-250 dark:border-slate-800 rounded-xl p-5 relative shadow-sm">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 mb-2">
                                                {{ $prop->subjectTeacher->type }}
                                            </span>
                                            <h3 class="font-bold text-slate-800 dark:text-white text-base leading-snug">{{ $prop->subjectTeacher->subject->name }}</h3>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">🏫 {{ __('Classe :') }} <strong class="text-slate-700 dark:text-slate-300">{{ $prop->timetable->classe->code_unique }}</strong></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 grid grid-cols-3 gap-2 bg-white dark:bg-slate-850 p-3 rounded-lg border border-slate-200/50 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-450">
                                        <div>
                                            <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ __('Jour') }}</span>
                                            <strong class="text-slate-700 dark:text-slate-300 capitalize">{{ __($prop->day_of_week) }}</strong>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ __('Créneau') }}</span>
                                            <strong class="text-slate-700 dark:text-slate-300">{{ $prop->slot_number }}</strong>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ __('Salle') }}</span>
                                            <strong class="text-slate-700 dark:text-slate-300">{{ $prop->room->name ?? '---' }}</strong>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-end space-x-2 pt-3 border-t border-slate-200/50 dark:border-slate-800">
                                        <button wire:click="openRefusal('{{ $prop->id }}')" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold shadow-sm transition-all">{{ __('Refuser...') }}</button>
                                        <button wire:click="confirmEntry('{{ $prop->id }}')" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm hover:shadow transition-all">{{ __('Confirmer') }}</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if($activeTab === 'availabilities')
                <!-- Edit Personal General Availabilities -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Déclarer Vos Disponibilités Générales') }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Spécifiez les tranches horaires de la semaine où vous êtes libre pour être programmé. Par défaut, si vous ne déclarez rien, vous êtes considéré libre.') }}</p>
                    </div>

                    <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                        <table class="w-full border-collapse text-left text-xs sm:text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="p-4 font-semibold uppercase tracking-wider text-center w-28">{{ __('Jours') }}</th>
                                    @foreach($slots as $slot)
                                        <th class="p-4 font-semibold uppercase tracking-wider text-center text-xs min-w-[100px]">{{ $slot['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'] as $dayKey)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10">
                                        <td class="p-4 font-bold text-slate-700 dark:text-slate-300 text-center uppercase text-xs">{{ __($dayKey) }}</td>
                                        @foreach($slots as $slot)
                                            <td class="p-2 text-center align-middle">
                                                @php
                                                    $slotLabel = $slot['label'];
                                                    $isSelected = $availGrid[$dayKey][$slotLabel] ?? false;
                                                @endphp
                                                <button type="button" wire:click="toggleAvail('{{ $dayKey }}', '{{ $slotLabel }}')"
                                                        class="w-10 h-10 mx-auto rounded-xl border font-bold flex items-center justify-center transition-all duration-200 {{ $isSelected ? 'bg-emerald-500 text-white border-emerald-500 hover:bg-emerald-650' : 'bg-slate-50 dark:bg-slate-850 text-slate-300 dark:text-slate-700 border-slate-200 dark:border-slate-800 hover:border-slate-400' }}">
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

                    <div class="flex justify-end pt-4">
                        <button wire:click="saveAvailabilities" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl shadow hover:shadow-md transition-all">
                            {{ __('Enregistrer mes Disponibilités') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Refusal modal with justification -->
    @if($showRefusalModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-850 dark:text-white">
                        ⚠️ {{ __('Motif du refus') }}
                    </h3>
                    <button wire:click="$set('showRefusalModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    @if($errorMessage)
                        <div class="p-3 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg text-xs font-semibold">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <div>
                        <p class="text-xs text-slate-500 mb-3">{{ __('Veuillez saisir le motif de votre indisponibilité pour ce créneau. Un processus intelligent d\'auto-remplacement recherchera une matière alternative éligible.') }}</p>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Raison de refus (min. 10 caractères)') }}</label>
                        <textarea wire:model="refusalReason" rows="4" class="w-full bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700 text-slate-850 dark:text-slate-100 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                        @error('refusalReason') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex justify-end space-x-2">
                    <button wire:click="$set('showRefusalModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">{{ __('Annuler') }}</button>
                    <button wire:click="submitRefusal" class="px-4 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow transition-all">{{ __('Confirmer le Refus') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
