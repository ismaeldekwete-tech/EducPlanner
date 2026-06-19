<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 transition-all">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 dark:bg-indigo-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-100 dark:shadow-none">
                        🎓
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ __('Espace Étudiant') }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Consultez votre emploi du temps hebdomadaire officiel et téléchargez le programme de votre classe.') }}</p>
                    </div>
                </div>

                @if(auth()->user()->classe)
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">{{ __('Classe :') }}</span>
                        <div class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium">
                            {{ auth()->user()->classe->code_unique }} ({{ auth()->user()->classe->filiere }} - {{ auth()->user()->classe->regime === 'J' ? __('Jour') : __('Soir') }})
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <label for="classe-select" class="text-sm font-semibold text-slate-600 dark:text-slate-400 min-w-max">{{ __('Classe :') }}</label>
                        <select id="classe-select" wire:model="selectedClasseId"
                                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-150 rounded-xl px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-white">
                            <option value="">-- {{ __('Choisir une classe') }} --</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->code_unique }} ({{ $classe->filiere }} - {{ $classe->regime === 'J' ? __('Jour') : __('Soir') }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        <!-- Timetable Grid -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                        @if($publishedTimetable)
                            {{ __('Emploi du Temps Officiel') }} - {{ $publishedTimetable->classe->code_unique }} ({{ __('Semaine') }} {{ $publishedTimetable->week_number }})
                        @else
                            {{ __('Emploi du Temps Hebdomadaire') }}
                        @endif
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Seuls les emplois du temps officiels validés et publiés par l\'administration de l\'IUC sont affichés ici.') }}</p>
                </div>

                @if($publishedTimetable)
                    <!-- Export PDF Button -->
                    <a href="{{ route('student.print', $selectedClasseId) }}" target="_blank"
                       class="flex items-center space-x-2 bg-slate-900 hover:bg-slate-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ __('Télécharger le PDF') }}</span>
                    </a>
                @endif
            </div>

            @if(!$selectedClasseId)
                <div class="text-center py-20 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                    <span class="text-4xl block mb-3">🏫</span>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">{{ __('Sélectionnez une classe') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('Veuillez sélectionner une classe dans la liste pour charger son emploi du temps officiel.') }}</p>
                </div>
            @elseif(!$publishedTimetable)
                <div class="text-center py-20 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                    <span class="text-4xl block mb-3">📅</span>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">{{ __('Aucun emploi du temps publié') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('L\'emploi du temps de cette semaine pour cette classe n\'a pas encore été officiellement publié par l\'administration.') }}</p>
                </div>
            @else
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
                                    <td class="p-4 font-bold text-slate-700 dark:text-slate-300 text-center bg-slate-50/30 dark:bg-slate-800/5 uppercase text-xs">{{ __($day) }}</td>
                                    @foreach($slots as $slot)
                                        @php
                                            $entry = collect($entries)->first(function ($e) use ($day, $slot) {
                                                return strtolower($e['day_of_week']) === strtolower($day) && $e['slot_number'] === $slot['label'];
                                            });
                                        @endphp
                                        <td class="p-2 align-middle text-center min-h-[90px]">
                                            @if($entry)
                                                @php
                                                    $type = $entry['subject_teacher']['type'] ?? 'CM';
                                                    $colorClass = $type === 'CM'
                                                        ? 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-800 dark:text-indigo-300 border-indigo-200 dark:border-indigo-900/60'
                                                        : ($type === 'TD'
                                                            ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/60'
                                                            : 'bg-rose-50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-900/60');
                                                    $badgeClass = $type === 'CM'
                                                        ? 'bg-indigo-200 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200'
                                                        : ($type === 'TD'
                                                            ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200'
                                                            : 'bg-rose-200 text-rose-800 dark:bg-rose-900 dark:text-rose-200');
                                                @endphp
                                                <div class="p-3 border rounded-xl shadow-sm text-left relative overflow-hidden {{ $colorClass }}">
                                                    <div class="flex items-center justify-between mb-1.5">
                                                        <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $badgeClass }}">{{ $type }}</span>
                                                    </div>
                                                    <div class="font-bold text-xs line-clamp-2 leading-snug mb-1">{{ $entry['subject_teacher']['subject']['name'] ?? 'N/A' }}</div>
                                                    <div class="text-[10px] opacity-80 font-medium truncate mb-1">  {{ $entry['subject_teacher']['teacher']['name'] ?? 'N/A' }}</div>
                                                    <div class="text-[9px] opacity-75 font-semibold">   {{ $entry['room']['name'] ?? 'N/A' }}</div>
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
            @endif
        </div>
    </div>
</div>
