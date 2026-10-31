<?php

use App\Http\Controllers\LaporBanjirController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => redirect()->route('laporan.create'));
Route::get('/lapor', [LaporBanjirController::class, 'create'])->name('laporan.create');
Route::post('/lapor', [LaporBanjirController::class, 'store'])->name('laporan.store');


Route::resource('laporan', LaporBanjirController::class)
    ->except(['create', 'store'])
    ->middleware('auth');


Route::get('/register', [RegisterController::class, 'create'])
    ->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest');

Route::get('/login', [LoginController::class, 'create'])
    ->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')->name('logout');

Route::get('/profil', fn () => view('auth.profil'))
    ->middleware('auth')->name('profil');
