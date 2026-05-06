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
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- WIDOK ADMINISTRATORA --}}
                @if(auth()->user()->hasRole('Administrator'))
                    <h3 class="text-lg font-bold mb-4">Użytkownicy w systemie</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">ID</th>
                                    <th class="py-2 px-4 border-b text-left">Imię i Email</th>
                                    <th class="py-2 px-4 border-b text-left">Status Konta</th>
                                    <th class="py-2 px-4 border-b text-left">Obecna Rola</th>
                                    <th class="py-2 px-4 border-b text-left">Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uzytkownicy as $uzytkownik)
                                    <tr class="hover:bg-gray-50 {{ !$uzytkownik->is_active ? 'bg-red-50' : '' }}">
                                        <td class="py-2 px-4 border-b">{{ $uzytkownik->id }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <strong>{{ $uzytkownik->name }}</strong><br>
                                            <span class="text-xs text-gray-500">{{ $uzytkownik->email }}</span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if($uzytkownik->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktywne</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Zablokowane</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <form action="{{ route('users.updateRole', $uzytkownik->id) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                <select name="role_id" class="text-sm rounded border-gray-300">
                                                    @foreach($wszystkieRole as $rola)
                                                        <option value="{{ $rola->id }}" {{ ($uzytkownik->roles->first()->id ?? 0) == $rola->id ? 'selected' : '' }}>
                                                            {{ $rola->nazwa }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">Zapisz</button>
                                            </form>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{-- Przycisk blokowania konta --}}
                                            @if($uzytkownik->id !== auth()->id()) {{-- Zabezpieczenie przed zablokowaniem samego siebie --}}
                                                <form action="{{ route('users.toggleBlock', $uzytkownik->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-bold py-1 px-2 rounded {{ $uzytkownik->is_active ? 'bg-red-500 hover:bg-red-700 text-white' : 'bg-green-500 hover:bg-green-700 text-white' }}">
                                                        {{ $uzytkownik->is_active ? 'Zablokuj' : 'Odblokuj' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                {{-- WIDOK ZADAŃ (TeamLeader / Pracownik) --}}
                @else
                    <h3 class="text-lg font-bold mb-4">Wszystkie zadania</h3>

                    @if(auth()->user()->hasRole('TeamLeader'))
                        <div class="mb-4">
                            <a href="{{ route('tasks.create') }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                + Dodaj nowe zadanie
                            </a>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Tytuł</th>
                                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Zespół</th>
                                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Termin</th>
                                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($zadania as $zadanie)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b text-sm font-bold text-gray-800">{{ $zadanie->tytul }}</td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-600">
                                            {{-- Wyświetlanie wielu pracowników --}}
                                            {{ $zadanie->users->pluck('name')->implode(', ') ?: 'Brak przypisanych' }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm">
                                            <select onchange="zmienStatus(this.value, {{ $zadanie->id }})" class="text-xs rounded border-gray-300 py-1 pl-2 pr-6 focus:ring-blue-500 bg-blue-50 text-blue-800 font-semibold cursor-pointer">
    <option value="Nowe" {{ $zadanie->status == 'Nowe' ? 'selected' : '' }}>Nowe</option>
    <option value="W trakcie" {{ $zadanie->status == 'W trakcie' ? 'selected' : '' }}>W trakcie</option>
    <option value="Zakończone" {{ $zadanie->status == 'Zakończone' ? 'selected' : '' }}>Zakończone</option>
</select>
<span id="msg-{{ $zadanie->id }}" class="text-xs text-green-600 font-bold ml-2 opacity-0 transition-opacity duration-500">Zapisano!</span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-700">{{ $zadanie->termin_wykonania }}</td>
                                        
                                        <td class="py-2 px-4 border-b text-sm text-gray-700 space-x-2">
    <a href="{{ route('tasks.show', $zadanie->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-xs">
        Szczegóły
    </a>

    @if(auth()->user()->hasRole('TeamLeader'))
        <a href="{{ route('tasks.edit', $zadanie->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-xs">
            Edytuj
        </a>
    @endif
</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">Brak zadań w bazie.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
    <script>
    function zmienStatus(nowyStatus, taskId) {
        console.log("Próba zmiany statusu dla zadania ID:", taskId, "na:", nowyStatus);

        // Szukamy tokena CSRF
        let csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            console.error("BŁĄD: Nie znaleziono tokena CSRF w nagłówku strony!");
            alert("Błąd konfiguracji bezpieczeństwa (brak CSRF).");
            return;
        }

        let csrfToken = csrfMeta.getAttribute('content');

        // Wysyłamy żądanie
        fetch(`/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: nowyStatus })
        })
        .then(response => {
            console.log("Odpowiedź serwera (kod):", response.status);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log("Sukces! Status zmieniony w bazie.");
                let msg = document.getElementById(`msg-${taskId}`);
                if (msg) {
                    msg.classList.remove('opacity-0');
                    setTimeout(() => msg.classList.add('opacity-0'), 2000);
                }
            } else {
                console.error("Serwer zwrócił błąd:", data.error);
                alert('Błąd: ' + (data.error || 'Nieznany błąd'));
            }
        })
        .catch(error => {
            console.error('Wystąpił błąd podczas wysyłania Fetch:', error);
            alert('Wystąpił błąd połączenia. Sprawdź konsolę (F12).');
        });
    }
</script>
</x-app-layout>