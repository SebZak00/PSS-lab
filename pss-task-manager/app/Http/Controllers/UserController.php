<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ==========================================
    // METODY POMOCNICZE (DRY & Security)
    // ==========================================

    private function ensureAdministrator()
    {
        if (!auth()->user()->hasRole('Administrator')) {
            abort(403, 'Brak uprawnień. Tylko Administrator ma dostęp do tej akcji.');
        }
    }

    // ==========================================
    // GŁÓWNE METODY KONTROLERA
    // ==========================================

    public function updateRole(Request $request, User $user)
    {
        $this->ensureAdministrator();

        // Zabezpieczenie przed zablokowaniem systemu: Admin nie może zmienić roli samemu sobie
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz zmienić uprawnień własnego konta!');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->roles()->sync([$request->role_id]);

        return back()->with('success', 'Rola użytkownika została zaktualizowana!');
    }

    public function toggleBlock(User $user)
    {
        $this->ensureAdministrator();
        
        // Zabezpieczenie: Admin nie może zablokować samego siebie
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz zablokować konta admina!');
        }
        
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Status konta został pomyślnie zmieniony.');
    }
}