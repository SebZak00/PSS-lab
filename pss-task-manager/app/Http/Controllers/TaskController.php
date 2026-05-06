<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Role;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ==========================================
    // METODY POMOCNICZE (DRY & Security)
    // ==========================================
    
    private function ensureTeamLeader()
    {
        if (!auth()->user()->hasRole('TeamLeader')) {
            abort(403, 'Brak uprawnień. Tylko TeamLeader ma dostęp do tej akcji.');
        }
    }

    private function ensureTaskAccess(Task $task)
    {
        $user = auth()->user();
        if (!$user->hasRole('TeamLeader') && !$task->users->contains($user->id)) {
            abort(403, 'Nie masz dostępu do tego zadania.');
        }
    }

    // ==========================================
    // GŁÓWNE METODY KONTROLERA
    // ==========================================

    public function index(Request $request)
    {
        $user = auth()->user();

        // WIDOK ADMINA (Dodajemy paginację)
        if ($user->hasRole('Administrator')) {
            $uzytkownicy = User::with('roles')->paginate(10);
            $wszystkieRole = Role::all();
            return view('dashboard', compact('uzytkownicy', 'wszystkieRole'));
        } 

        // WIDOK PRACOWNIKA / TEAM LEADERA
        $query = $user->hasRole('TeamLeader') 
            ? Task::with('users') 
            : $user->tasks()->with('users');

        // --- WYSZUKIWANIE I FILTROWANIE ---
        if ($request->filled('search')) {
            $query->where('tytul', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- PAGINACJA (Zamiast ->get() dajemy ->paginate()) ---
        $zadania = $query->paginate(5); 

        return view('dashboard', compact('zadania'));
    }

    public function create()
    {
        $this->ensureTeamLeader();
        $pracownicy = User::all(); 
        return view('tasks.create', compact('pracownicy'));
    }

    public function store(Request $request)
    {
        $this->ensureTeamLeader();

        $validated = $request->validate([
            'tytul' => 'required|max:255',
            'opis' => 'required',
            // WALIDACJA KONTEKSTOWA: Data wykonania od dziś w przód!
            'termin_wykonania' => 'required|date|after_or_equal:today',
            'przypisani' => 'required|array',
            'przypisani.*' => 'exists:users,id',
        ], [
            'termin_wykonania.after_or_equal' => 'Błąd: Termin wykonania zadania nie może być datą z przeszłości!'
        ]);

        $task = Task::create([
            'tytul' => $validated['tytul'],
            'opis' => $validated['opis'],
            'status' => 'Nowe',
            'termin_wykonania' => $validated['termin_wykonania'],
            'id_tworcy' => auth()->id(),
        ]);

        $task->users()->attach($validated['przypisani']);

        return redirect()->route('dashboard')->with('success', 'Zadanie dodane i przypisane do zespołu!');
    }

    public function edit(Task $task)
    {
        $this->ensureTeamLeader();
        $pracownicy = User::all();
        return view('tasks.edit', compact('task', 'pracownicy'));
    }

    public function update(Request $request, Task $task)
    {
        $this->ensureTeamLeader();

        $validated = $request->validate([
            'tytul' => 'required|max:255',
            'opis' => 'required',
            'status' => 'required|string',
            // WALIDACJA KONTEKSTOWA
            'termin_wykonania' => 'required|date|after_or_equal:today',
            'przypisani' => 'array', 
            'przypisani.*' => 'exists:users,id',
        ], [
            'termin_wykonania.after_or_equal' => 'Błąd: Termin wykonania zadania nie może być datą z przeszłości!'
        ]);

        $task->update([
            'tytul' => $validated['tytul'],
            'opis' => $validated['opis'],
            'status' => $validated['status'],
            'termin_wykonania' => $validated['termin_wykonania'],
        ]);

        $task->users()->sync($request->przypisani ?? []);

        return redirect()->route('dashboard')->with('success', 'Zadanie zostało pomyślnie zaktualizowane!');
    }

    public function show(Task $task)
    {
        $this->ensureTaskAccess($task);
        $task->load(['users', 'notes.user']); 

        return view('tasks.show', compact('task'));
    }

    public function addNote(Request $request, Task $task)
    {
        $this->ensureTaskAccess($task);

        $request->validate([
            'tresc' => 'required|string',
            'typ' => 'required|in:zwykla_notatka,prosba_o_ddl'
        ]);

        $task->notes()->create([
            'user_id' => auth()->id(),
            'tresc' => $request->tresc,
            'typ' => $request->typ
        ]);

        return back()->with('success', 'Wiadomość została dodana!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $user = auth()->user();
        if (!$user->hasRole('TeamLeader') && !$task->users->contains($user->id)) {
            return response()->json(['error' => 'Brak uprawnień do zmiany statusu tego zadania'], 403);
        }

        $request->validate([
            'status' => 'required|string|max:50'
        ]);

        $task->update(['status' => $request->status]);

        return response()->json(['success' => true, 'nowy_status' => $task->status]);
    }
}