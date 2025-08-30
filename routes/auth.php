<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'loginToSpotify'])->name('login');

Route::get('callbackLoginFromSpotify', [LoginController::class, 'callbackLoginFromSpotify'])->name('callbackLogin');
