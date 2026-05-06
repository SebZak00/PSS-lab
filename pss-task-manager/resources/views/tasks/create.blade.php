<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dodaj nowe zadanie</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">Tytuł zadania</label>
                        <input type="text" name="tytul" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Opis</label>
                        <textarea name="opis" class="w-full rounded-md border-gray-300 shadow-sm" rows="3" required></textarea>
                    </div>

                    <div class="mb-4">
    <label class="block text-gray-700 font-bold mb-2">Przypisz do (możesz wybrać wielu):</label>
    <div class="space-y-2 bg-gray-50 p-4 border rounded-md">
        @foreach($pracownicy as $p)
            <label class="inline-flex items-center block">
                <input type="checkbox" name="przypisani[]" value="{{ $p->id }}" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <span class="ml-2 text-gray-700">{{ $p->name }} ({{ $p->email }})</span>
            </label><br>
        @endforeach
    </div>
</div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Termin wykonania</label>
                        <input type="date" name="termin_wykonania" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Zapisz zadanie
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>