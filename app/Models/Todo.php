<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'todo_list_id',

        'title',
        'description',

        'status',

        'priority',

        'is_favorite',

        'due_date',
        'reminder_at',

        'started_at',
        'completed_at',
        'archived_at',

        'position',

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
            'is_favorite' => 'boolean',
            'position' => 'integer',
            'version' => 'integer',

            // Dates
            'due_date' => 'date',
            'reminder_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'archived_at' => 'datetime',

            // Offline First
            'is_synced' => 'boolean',
            'client_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'deleted_at_client' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function list()
    {
        return $this->belongsTo(TodoList::class, 'todo_list_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function changes()
    {
        return $this->hasMany(TodoChange::class);
    }
}
