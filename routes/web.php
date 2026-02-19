<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::livewire('admin', 'pages::admin.dashboard')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.dashboard');

Route::livewire('admin/members', 'pages::admin.members.index')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.members.index');

Route::livewire('admin/members/{member}', 'pages::admin.members.show')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.members.show');

require __DIR__.'/settings.php';
