<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ==========================================
    // WŁAŚCIWOŚCI
    // ==========================================

    protected $fillable = [
        'name',
        'email',
        'password',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // RELACJE
    // ==========================================

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==========================================
    // METODY POMOCNICZE
    // ==========================================

    public function hasRole($roleName)
    {
        return $this->roles->contains('nazwa', $roleName);
    }

    // ==========================================
    // EVENTY (AUTOMATY)
    // ==========================================

    protected static function booted()
    {
        static::creating(function ($user) {
            if (auth()->check()) {
                $user->created_by = auth()->id();
                $user->updated_by = auth()->id();
            }
        });

        static::updating(function ($user) {
            if (auth()->check()) {
                $user->updated_by = auth()->id();
            }
        });
    }
}