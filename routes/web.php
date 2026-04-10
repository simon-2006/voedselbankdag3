<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AllergieController;
use App\Http\Controllers\LeverancierController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/gezinsallergieen', [AllergieController::class, 'index'])->name('allergie.index');
    Route::get('/gezinsallergieen/gezin/{gezin}', [AllergieController::class, 'showGezin'])->name('allergie.gezin');
    Route::get('/gezinsallergieen/gezin/{gezin}/persoon/{persoon}/wijzig', [AllergieController::class, 'edit'])->name('allergie.edit');
    Route::post('/gezinsallergieen/gezin/{gezin}/persoon/{persoon}/wijzig', [AllergieController::class, 'update'])->name('allergie.update');

    Route::get('/leverancier', [LeverancierController::class, 'index'])->name('leverancier.index');
    Route::get('/leverancier/{leverancierId}/producten', [ProductController::class, 'getProductenByLeverancier'])->name('leverancier.producten');
    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');

    Route::post('/uitloggen', [AuthController::class, 'logout'])->name('logout');
});
