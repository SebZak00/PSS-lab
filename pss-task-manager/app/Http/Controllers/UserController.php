<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function updateRole(Request $request, User $user)
    {
        // Tylko Admin może to zrobić
        if (!auth()->user()->hasRole('Administrator')) {
            abort(403, 'Brak uprawnień');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        // Funkcja sync() automatycznie podmieni starą rolę na nową w tabeli role_user
        $user->roles()->sync([$request->role_id]);

        return back()->with('success', 'Rola użytkownika została zaktualizowana!');
    }
    public function toggleBlock(User $user)
{
    if (!auth()->user()->hasRole('Administrator')) abort(403);
    
    // Odwracamy status: jeśli był true, będzie false
    $user->is_active = !$user->is_active;
    $user->save();

    return back()->with('success', 'Status konta zmieniony.');
}
}