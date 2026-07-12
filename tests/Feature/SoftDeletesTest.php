<?php

use App\Models\Device;
use App\Models\SyncLog;
use App\Models\Tag;
use App\Models\TagTodo;
use App\Models\Todo;
use App\Models\TodoChange;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

test('every model uses the SoftDeletes trait', function () {
    $models = [
        User::class,
        Todo::class,
        TodoList::class,
        Tag::class,
        Device::class,
        SyncLog::class,
        TodoChange::class,
        TagTodo::class,
    ];

    foreach ($models as $model) {
        $traits = class_uses_recursive($model);

        expect(in_array(SoftDeletes::class, $traits, true))->toBeTrue("{$model} is missing the SoftDeletes trait");
    }
});

test('a device is soft-deleted, not removed, when revoked', function () {
    $device = Device::factory()->create();

    $device->delete();

    expect(Device::find($device->id))->toBeNull();
    expect(Device::withTrashed()->find($device->id))->not->toBeNull();
    expect($device->fresh()->trashed())->toBeTrue();
});

test('a sync log is soft-deletable', function () {
    $user = User::factory()->create();
    $log = SyncLog::create([
        'user_id' => $user->id,
        'status' => 'success',
        'uploaded' => 0,
        'downloaded' => 0,
        'conflicts' => 0,
    ]);

    $log->delete();

    expect(SyncLog::find($log->id))->toBeNull();
    expect(SyncLog::withTrashed()->find($log->id))->not->toBeNull();
});

test('a todo change is soft-deletable', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();
    $change = TodoChange::create([
        'uuid' => Str::uuid(),
        'user_id' => $user->id,
        'todo_id' => $todo->id,
        'action' => 'created',
    ]);

    $change->delete();

    expect(TodoChange::find($change->id))->toBeNull();
    expect(TodoChange::withTrashed()->find($change->id))->not->toBeNull();
});
