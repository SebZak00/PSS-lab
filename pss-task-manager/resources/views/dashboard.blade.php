<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(auth()->user()->hasRole('Administrator'))
                {{ __('Panel Administratora - Zarządzanie Użytkownikami') }}
            @else
                {{ __('Lista Zadań') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- KOMUNIKATY SESYJNE --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm">
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 shadow-sm">
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ========================================== --}}
                {{-- WIDOK ADMINISTRATORA                       --}}
                {{-- ========================================== --}}
                @if(auth()->user()->hasRole('Administrator'))
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Użytkownicy w systemie</h3>
                    
                    <div class="overflow-x-auto ring-1 ring-black ring-opacity-5 rounded-lg shadow-sm mb-4">
                        <table class="min-w-full bg-white border-collapse">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Imię i Email</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Konta</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Obecna Rola</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Akcje</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($uzytkownicy as $uzytkownik)
                                    <tr class="hover:bg-gray-50 transition-colors {{ !$uzytkownik->is_active ? 'bg-red-50' : '' }}">
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $uzytkownik->id }}</td>
                                        <td class="py-3 px-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $uzytkownik->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $uzytkownik->email }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($uzytkownik->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktywne</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Zablokowane</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <form action="{{ route('users.updateRole', $uzytkownik->id) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                <select name="role_id" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                                    @foreach($wszystkieRole as $rola)
                                                        <option value="{{ $rola->id }}" {{ ($uzytkownik->roles->first()->id ?? 0) == $rola->id ? 'selected' : '' }}>
                                                            {{ $rola->nazwa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-colors">
                                                    Zapisz
                                                </button>
                                            </form>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($uzytkownik->id !== auth()->id())
                                                <form action="{{ route('users.toggleBlock', $uzytkownik->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-bold py-1.5 px-3 rounded shadow transition-colors {{ $uzytkownik->is_active ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                                        {{ $uzytkownik->is_active ? 'Zablokuj' : 'Odblokuj' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Twoje konto</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginacja użytkowników --}}
                    <div class="mt-4">
                        {{ $uzytkownicy->links() }}
                    </div>

                {{-- ========================================== --}}
                {{-- WIDOK ZADAŃ (TeamLeader / Pracownik)       --}}
                {{-- ========================================== --}}
                @else
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 border-b pb-4">
                        <h3 class="text-xl font-bold text-gray-800">Wszystkie zadania</h3>
                        @if(auth()->user()->hasRole('TeamLeader'))
                            <a href="{{ route('tasks.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Dodaj nowe zadanie
                            </a>
                        @endif
                    </div>

                    {{-- FORMULARZ WYSZUKIWANIA I FILTROWANIA --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-wrap gap-4 items-end shadow-sm">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs text-gray-600 uppercase font-bold mb-1">Szukaj po tytule</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Wpisz fragment tytułu..." class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                        <div class="w-48">
                            <label class="block text-xs text-gray-600 uppercase font-bold mb-1">Filtruj po statusie</label>
                            <select name="status" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                <option value="">Wszystkie statusy</option>
                                <option value="Nowe" {{ request('status') == 'Nowe' ? 'selected' : '' }}>Nowe</option>
                                <option value="W trakcie" {{ request('status') == 'W trakcie' ? 'selected' : '' }}>W trakcie</option>
                                <option value="Zakończone" {{ request('status') == 'Zakończone' ? 'selected' : '' }}>Zakończone</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm shadow transition-colors">
                                Szukaj
                            </button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('dashboard') }}" class="text-red-500 hover:text-red-700 font-semibold text-sm transition-colors px-2">
                                    Wyczyść
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- TABELA ZADAŃ --}}
                    <div class="overflow-x-auto ring-1 ring-black ring-opacity-5 rounded-lg shadow-sm mb-4">
                        <table class="min-w-full bg-white border-collapse">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tytuł</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Zespół</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Termin</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Akcje</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($zadania as $zadanie)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-4 text-sm font-bold text-gray-800">{{ $zadanie->tytul }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            {{ $zadanie->users->pluck('name')->implode(', ') ?: 'Brak przypisanych' }}
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <div class="flex items-center">
                                                <select onchange="zmienStatus(this.value, {{ $zadanie->id }})" class="text-xs rounded-md border-gray-300 py-1 pl-2 pr-6 focus:ring-blue-500 bg-blue-50 text-blue-800 font-semibold cursor-pointer shadow-sm">
                                                    <option value="Nowe" {{ $zadanie->status == 'Nowe' ? 'selected' : '' }}>Nowe</option>
                                                    <option value="W trakcie" {{ $zadanie->status == 'W trakcie' ? 'selected' : '' }}>W trakcie</option>
                                                    <option value="Zakończone" {{ $zadanie->status == 'Zakończone' ? 'selected' : '' }}>Zakończone</option>
                                                </select>
                                                <span id="msg-{{ $zadanie->id }}" class="text-xs text-green-600 font-bold ml-2 opacity-0 transition-opacity duration-500">
                                                    Zapisano!
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-sm font-medium {{ $zadanie->termin_wykonania < now()->toDateString() ? 'text-red-600' : 'text-gray-700' }}">
                                            {{ $zadanie->termin_wykonania }}
                                        </td>
                                        <td class="py-3 px-4 text-sm space-x-2 flex items-center">
                                            <a href="{{ route('tasks.show', $zadanie->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1.5 px-3 rounded text-xs shadow transition-colors">
                                                Szczegóły
                                            </a>
                                            
                                            @if(auth()->user()->hasRole('TeamLeader'))
                                                <a href="{{ route('tasks.edit', $zadanie->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1.5 px-3 rounded text-xs shadow transition-colors">
                                                    Edytuj
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-500 italic">Brak zadań pasujących do kryteriów.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginacja zadań --}}
                    <div class="mt-4">
                        {{ $zadania->withQueryString()->links() }}
                    </div>

                @endif

            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- SKRYPTY AJAX                               --}}
    {{-- ========================================== --}}
    <script>
        function zmienStatus(nowyStatus, taskId) {
            let csrfMeta = document.querySelector('meta[name="csrf-token"]');
            
            if (!csrfMeta) {
                alert("Błąd: Nie znaleziono tokena CSRF w nagłówku strony!");
                return;
            }

            let csrfToken = csrfMeta.getAttribute('content');

            fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: nowyStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let msg = document.getElementById(`msg-${taskId}`);
                    if (msg) {
                        msg.classList.remove('opacity-0');
                        setTimeout(() => msg.classList.add('opacity-0'), 2000);
                    }
                } else {
                    alert('Błąd: ' + (data.error || 'Brak uprawnień.'));
                }
            })
            .catch(error => {
                console.error('Wystąpił błąd AJAX:', error);
                alert('Wystąpił błąd połączenia z serwerem.');
            });
        }
    </script>
</x-app-layout>