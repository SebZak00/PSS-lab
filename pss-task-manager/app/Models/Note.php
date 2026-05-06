<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'user_id', 'tresc', 'typ'];

    // Notatka należy do konkretnego użytkownika (autora)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Notatka należy do konkretnego zadania
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}