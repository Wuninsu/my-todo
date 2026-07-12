<?php

use App\Models\Device;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('a user can only update or delete their own todo', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $todo = Todo::factory()->for($owner)->create();

    expect(Gate::forUser($owner)->allows('update', $todo))->toBeTrue();
    expect(Gate::forUser($owner)->allows('delete', $todo))->toBeTrue();

    expect(Gate::forUser($stranger)->allows('update', $todo))->toBeFalse();
    expect(Gate::forUser($stranger)->allows('delete', $todo))->toBeFalse();
});

test('a user can only restore or force-delete their own trashed todo', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $todo = Todo::factory()->for($owner)->create();
    $todo->delete();

    expect(Gate::forUser($owner)->allows('restore', $todo))->toBeTrue();
    expect(Gate::forUser($owner)->allows('forceDelete', $todo))->toBeTrue();

    expect(Gate::forUser($stranger)->allows('restore', $todo))->toBeFalse();
    expect(Gate::forUser($stranger)->allows('forceDelete', $todo))->toBeFalse();
});

test('a user can only update or delete their own tag', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $tag = Tag::factory()->for($owner)->create();

    expect(Gate::forUser($owner)->allows('update', $tag))->toBeTrue();
    expect(Gate::forUser($owner)->allows('delete', $tag))->toBeTrue();

    expect(Gate::forUser($stranger)->allows('update', $tag))->toBeFalse();
    expect(Gate::forUser($stranger)->allows('delete', $tag))->toBeFalse();
});

test('a user can only update or delete their own todo list', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $list = TodoList::factory()->for($owner)->create();

    expect(Gate::forUser($owner)->allows('update', $list))->toBeTrue();
    expect(Gate::forUser($owner)->allows('delete', $list))->toBeTrue();

    expect(Gate::forUser($stranger)->allows('update', $list))->toBeFalse();
    expect(Gate::forUser($stranger)->allows('delete', $list))->toBeFalse();
});

test('a user can only restore or force-delete their own trashed list, tag, or device', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $list = TodoList::factory()->for($owner)->create();
    $list->delete();
    $tag = Tag::factory()->for($owner)->create();
    $tag->delete();
    $device = Device::factory()->for($owner)->create();
    $device->delete();

    foreach ([$list, $tag, $device] as $record) {
        expect(Gate::forUser($owner)->allows('restore', $record))->toBeTrue();
        expect(Gate::forUser($owner)->allows('forceDelete', $record))->toBeTrue();

        expect(Gate::forUser($stranger)->allows('restore', $record))->toBeFalse();
        expect(Gate::forUser($stranger)->allows('forceDelete', $record))->toBeFalse();
    }
});

test('a user can only delete their own device', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $device = Device::factory()->for($owner)->create();

    expect(Gate::forUser($owner)->allows('delete', $device))->toBeTrue();
    expect(Gate::forUser($stranger)->allows('delete', $device))->toBeFalse();
});

test('only admins can view, update, or delete other user accounts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $regular = User::factory()->create(['role' => 'user']);
    $target = User::factory()->create(['role' => 'user']);

    expect(Gate::forUser($admin)->allows('viewAny', User::class))->toBeTrue();
    expect(Gate::forUser($admin)->allows('view', $target))->toBeTrue();
    expect(Gate::forUser($admin)->allows('update', $target))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete', $target))->toBeTrue();

    expect(Gate::forUser($regular)->allows('viewAny', User::class))->toBeFalse();
    expect(Gate::forUser($regular)->allows('view', $target))->toBeFalse();
    expect(Gate::forUser($regular)->allows('update', $target))->toBeFalse();
    expect(Gate::forUser($regular)->allows('delete', $target))->toBeFalse();
});

test('an admin cannot delete their own account via the policy', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect(Gate::forUser($admin)->allows('delete', $admin))->toBeFalse();
});
