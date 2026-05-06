<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edycja zadania: {{ $task->tytul }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                {{-- Ważne: w metodzie PUT musimy dodać @method('PUT') --}}
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700">Tytuł zadania</label>
                        <input type="text" name="tytul" value="{{ $task->tytul }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Opis</label>
                        <textarea name="opis" class="w-full rounded-md border-gray-300 shadow-sm" rows="3" required>{{ $task->opis }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="Nowe" {{ $task->status == 'Nowe' ? 'selected' : '' }}>Nowe</option>
                            <option value="W trakcie" {{ $task->status == 'W trakcie' ? 'selected' : '' }}>W trakcie</option>
                            <option value="Zakończone" {{ $task->status == 'Zakończone' ? 'selected' : '' }}>Zakończone</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Zespół pracujący nad zadaniem:</label>
                        <div class="space-y-2 bg-gray-50 p-4 border rounded-md">
                            @foreach($pracownicy as $p)
                                <label class="inline-flex items-center block">
                                    <input type="checkbox" name="przypisani[]" value="{{ $p->id }}" 
                                        {{ $task->users->contains($p->id) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                                    <span class="ml-2 text-gray-700">{{ $p->name }} ({{ $p->email }})</span>
                                </label><br>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Termin wykonania</label>
                        <input type="date" name="termin_wykonania" value="{{ $task->termin_wykonania }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>

                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                        Zaktualizuj zadanie
                    </button>
                    <a href="{{ route('dashboard') }}" class="ml-4 text-gray-600 hover:underline">Anuluj</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>