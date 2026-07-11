<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'avatar',
        'timezone',
        'theme',
        'role',

        // Offline First
        'is_synced',
        'version',
        'client_updated_at',
        'last_synced_at',
        'device_uuid',
        'deleted_at_client',
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
            // Offline First
            'is_synced' => 'boolean',
            'version' => 'integer',
            'client_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'deleted_at_client' => 'datetime',
        ];
    }

    public function todoLists()
    {
        return $this->hasMany(TodoList::class);
    }

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class);
    }

    public function todoChanges()
    {
        return $this->hasMany(TodoChange::class);
    }
}
