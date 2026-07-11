<?php

use App\Livewire\Main\Admin\Overview;
use App\Livewire\Main\Admin\UserEdit;
use App\Livewire\Main\Admin\UserIndex;
use App\Livewire\Main\Admin\UserView;
use App\Livewire\Main\Dashboard;
use App\Livewire\Main\ProfileIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', ProfileIndex::class)->name('profile');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Overview::class)->name('dashboard');
    Route::get('/users', UserIndex::class)->name('users');
    Route::get('/users/{user}', UserView::class)->name('users.view');
    Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
});

require __DIR__.'/auth.php';
