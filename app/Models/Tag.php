<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',

        'name',
        'color',

        // Offline First
        'is_synced',
        'version',
        'client_updated_at',
        'last_synced_at',
        'device_uuid',
        'deleted_at_client',
    ];

    protected function casts(): array
    {
        return [
            'is_synced' => 'boolean',
            'version' => 'integer',
            'client_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'deleted_at_client' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function todos()
    {
        return $this->belongsToMany(Todo::class, 'tag_todos');
    }

    public function isSystem(): bool
    {
        return is_null($this->user_id);
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * A user's own tags plus every system tag — the full set of tags
     * available for them to use or choose from.
     */
    public function scopeAvailableTo($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        });
    }
}
