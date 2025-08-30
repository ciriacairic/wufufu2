<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function ($router) {
    $router->get('/', function () {
        return view('welcome');
    })->name('welcome');
});

Route::fallback(function () {
    return redirect()->route('home');
});

//require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
