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
}
