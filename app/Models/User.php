<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Mass assignable attributes
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Hidden fields when returned in JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts for attributes
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 10+ handles hashing automatically
        ];
    }

    // Relationship: A user has many tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
