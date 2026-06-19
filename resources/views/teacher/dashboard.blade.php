<div class="max-w-4xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Mes Plannings Hebdomadaires</h2>
    <div class="grid gap-4">
        @foreach($myEntries as $entry)
            <div class="bg-white p-4 border rounded-lg shadow-sm flex justify-between items-center">
                <div>
                    <p class="font-bold">{{ $entry->subjectTeacher->subject->name }}</p>
                    <p class="text-sm text-gray-600">{{ $entry->day_of_week }} - Slot {{ $entry->slot_number }}</p>
                </div>
                <div class="space-x-2">
                    <form action="{{ route('teacher.confirm', $entry->id) }}" method="POST">
                        @csrf
                        <button class="bg-green-500 text-white px-3 py-1 rounded">Confirmer</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
