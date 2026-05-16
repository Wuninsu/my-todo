<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
   use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',

        'uploaded',
        'downloaded',
        'conflicts',

        'status',
        'message',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded' => 'integer',
            'downloaded' => 'integer',
            'conflicts' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
