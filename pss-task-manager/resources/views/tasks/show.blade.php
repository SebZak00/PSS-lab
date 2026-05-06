<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Szczegóły zadania') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; Wróć do listy
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- ============================== --}}
            {{-- LEWA KOLUMNA: Informacje       --}}
            {{-- ============================== --}}
            <div class="md:col-span-1 bg-white p-6 shadow-sm sm:rounded-lg h-fit">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $task->tytul }}</h3>
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold mb-4">
                    Status: {{ $task->status }}
                </span>

                <div class="mb-4">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide">Opis zadania</h4>
                    <p class="text-gray-700 mt-1">{{ $task->opis }}</p>
                </div>

                <div class="mb-4">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide">Termin wykonania</h4>
                    <p class="text-red-600 font-bold mt-1">{{ $task->termin_wykonania }}</p>
                </div>

                <div class="mb-4">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide">Zespół (Przypisani)</h4>
                    <ul class="mt-1 space-y-1">
                        @forelse($task->users as $pracownik)
                            <li class="text-gray-700 font-medium flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                {{ $pracownik->name }}
                            </li>
                        @empty
                            <li class="text-gray-500 text-sm">Brak przypisanych osób.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- ============================== --}}
            {{-- PRAWA KOLUMNA: Notatki / Czat  --}}
            {{-- ============================== --}}
            <div class="md:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg flex flex-col">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Dyskusja i Notatki</h3>

                {{-- Lista notatek --}}
                <div class="flex-1 overflow-y-auto mb-6 space-y-4 max-h-96 pr-2">
                    @forelse($task->notes as $note)
                        <div class="p-4 rounded-lg {{ $note->typ == 'prosba_o_ddl' ? 'bg-orange-50 border-l-4 border-orange-500' : 'bg-gray-50 border border-gray-200' }}">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-sm text-gray-800">{{ $note->user->name }}</span>
                                <span class="text-xs text-gray-500">{{ $note->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            @if($note->typ == 'prosba_o_ddl')
                                <span class="inline-block px-2 py-0.5 bg-orange-200 text-orange-800 rounded text-xs font-bold mb-2">
                                    ⚠️ Prośba o zmianę terminu
                                </span>
                            @endif
                            <p class="text-gray-700 text-sm">{{ $note->tresc }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center italic py-4">Brak notatek. Rozpocznij dyskusję!</p>
                    @endforelse
                </div>

                {{-- Formularz dodawania nowej notatki --}}
                <form action="{{ route('tasks.addNote', $task->id) }}" method="POST" class="mt-auto border-t pt-4">
                    @csrf
                    <div class="flex flex-col gap-3">
                        <textarea name="tresc" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm" placeholder="Napisz wiadomość lub dodaj notatkę..." required></textarea>
                        
                        <div class="flex justify-between items-center">
                            <select name="typ" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="zwykla_notatka">Zwykła notatka</option>
                                <option value="prosba_o_ddl">Prośba o zmianę terminu</option>
                            </select>
                            
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md transition duration-150">
                                Wyślij
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>