<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task; // Importujemy model zadań
use Illuminate\Http\Request;


class TaskController extends Controller
{
    public function index()
{
    $user = auth()->user();

    if ($user->hasRole('Administrator')) {
        $uzytkownicy = User::with('roles')->get();
        $wszystkieRole = \App\Models\Role::all();
        return view('dashboard', compact('uzytkownicy', 'wszystkieRole'));
    } 

    if ($user->hasRole('TeamLeader')) {
        // TeamLeader widzi wszystko
        $zadania = Task::with('users')->get();
    } else {
        // Pracownik widzi tylko zadania, do których jest przypisany
        $zadania = $user->tasks()->with('users')->get();
    }

    return view('dashboard', compact('zadania'));
}

    public function create()
    {
        // ZABIERAMY DOSTĘP ADMINOWI - tylko TeamLeader ma tu wstęp
        if (!auth()->user()->hasRole('TeamLeader')) {
            abort(403, 'Tylko TeamLeader może dodawać nowe zadania.');
        }

        $pracownicy = \App\Models\User::all(); 
        return view('tasks.create', compact('pracownicy'));
    }

public function store(Request $request)
{
    // Walidujemy, oczekując tablicy ID przypisanych użytkowników
    $validated = $request->validate([
        'tytul' => 'required|max:255',
        'opis' => 'required',
        'termin_wykonania' => 'required|date',
        'przypisani' => 'required|array', // Zmieniliśmy na tablicę!
        'przypisani.*' => 'exists:users,id',
    ]);

    // Tworzymy zadanie
    $task = Task::create([
        'tytul' => $validated['tytul'],
        'opis' => $validated['opis'],
        'status' => 'Nowe',
        'termin_wykonania' => $validated['termin_wykonania'],
        'id_tworcy' => auth()->id(),
    ]);

    // Magia relacji Wiele-do-Wielu: Przypisujemy wszystkich zaznaczonych pracowników
    $task->users()->attach($validated['przypisani']);

    return redirect()->route('dashboard')->with('success', 'Zadanie dodane i przypisane do zespołu!');
}
public function edit(Task $task)
    {
        // Tylko TeamLeader ma prawo edytować zadania
        if (!auth()->user()->hasRole('TeamLeader')) {
            abort(403, 'Brak uprawnień do edycji zadań.');
        }

        $pracownicy = User::all();
        // Przekazujemy konkretne zadanie i listę pracowników do widoku
        return view('tasks.edit', compact('task', 'pracownicy'));
    }

    public function update(Request $request, Task $task)
    {
        if (!auth()->user()->hasRole('TeamLeader')) {
            abort(403, 'Brak uprawnień do edycji zadań.');
        }

        $validated = $request->validate([
            'tytul' => 'required|max:255',
            'opis' => 'required',
            'status' => 'required|string',
            'termin_wykonania' => 'required|date',
            'przypisani' => 'array', // Może być puste, jeśli odznaczymy wszystkich
            'przypisani.*' => 'exists:users,id',
        ]);

        // Aktualizacja głównych danych zadania
        $task->update([
            'tytul' => $validated['tytul'],
            'opis' => $validated['opis'],
            'status' => $validated['status'],
            'termin_wykonania' => $validated['termin_wykonania'],
        ]);

        // Synchronizacja przypisanych użytkowników (nadpisuje stare powiązania nowymi)
        $task->users()->sync($request->przypisani ?? []);

        return redirect()->route('dashboard')->with('success', 'Zadanie zostało pomyślnie zaktualizowane!');
    }
    public function show(Task $task)
    {
        $user = auth()->user();
        $isTL = $user->hasRole('TeamLeader');
        $isAssigned = $task->users->contains($user->id);

        // Zabezpieczenie: Zadanie mogą oglądać tylko przypisani pracownicy i TL
        if (!$isTL && !$isAssigned) {
            abort(403, 'Nie masz dostępu do tego zadania.');
        }

        // Pobieramy zadanie razem z jego przypisanymi ludźmi oraz notatkami (i autorami tych notatek)
        $task->load(['users', 'notes.user']); 

        return view('tasks.show', compact('task'));
    }

    public function addNote(Request $request, Task $task)
    {
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
        
        $isTL = $user->hasRole('TeamLeader');
        $isAssigned = $task->users->contains($user->id);

        if (!$isTL && !$isAssigned) {
            return response()->json(['error' => 'Brak uprawnień do zmiany statusu tego zadania'], 403);
        }

        $request->validate([
            'status' => 'required|string|max:50'
        ]);

        $task->update(['status' => $request->status]);

        return response()->json(['success' => true, 'nowy_status' => $task->status]);
    }
}