<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Jeśli używasz SoftDeletes (miękkie usuwanie), to pewnie masz tu też ten import

class Task extends Model
{
    use HasFactory;
    // use SoftDeletes; // (jeśli dodałeś to wcześniej)

    // ODBLOKOWANIE PÓL DLA FORMULARZA
    protected $fillable = [
        'tytul', 
        'opis', 
        'status', 
        'termin_wykonania', 
        'id_tworcy'
    ];
    // Zadanie może mieć wielu przypisanych użytkowników
public function users()
{
    return $this->belongsToMany(User::class, 'task_user');
}

// Zadanie ma jednego twórcę (TeamLeadera)
public function creator()
{
    return $this->belongsTo(User::class, 'id_tworcy');
}
// Zadanie może mieć wiele notatek
public function notes()
{
    return $this->hasMany(Note::class);
}
}
