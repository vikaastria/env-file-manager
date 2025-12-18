<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\EnvFiles\Index;
use App\Livewire\EnvFiles\Create;
use App\Livewire\EnvFiles\Edit;
use App\Livewire\EnvFiles\Show;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Env Files Routes
Route::middleware(['auth', 'verified'])->prefix('env-files')->name('env-files.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/create', Create::class)->name('create');
    Route::get('/{envFile}/edit', Edit::class)->name('edit');
    Route::get('/{envFile}', Show::class)->name('show');
});

require __DIR__.'/auth.php';
