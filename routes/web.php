<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoorraadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllergieController;
use App\Http\Controllers\VoedselpakketOverzichtController;



Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/registreren', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registreren', [AuthController::class, 'register']);

    Route::get('/inloggen', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/inloggen', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/voorraad',             [VoorraadController::class, 'index'])->name('voorraad.index');
    Route::get('/voorraad/{id}',        [VoorraadController::class, 'show'])->name('voorraad.show');
    Route::get('/voorraad/{id}/wijzig', [VoorraadController::class, 'edit'])->name('voorraad.edit');
    Route::put('/voorraad/{id}',        [VoorraadController::class, 'update'])->name('voorraad.update');
    
    

    Route::post('/uitloggen', [AuthController::class, 'logout'])->name('logout');

    Route::get('/allergieen', [AllergieController::class, 'index'])->name('allergie.index');

    Route::get('/voedselpakketten', [VoedselpakketOverzichtController::class, 'index'])->name('voedselpakketten.index');

});
