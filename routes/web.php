<?php

use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\WebScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return redirect('/docs/index.html');
});

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth', 'role:admin,teacher'])->group(function (): void {
    Route::get('/schedules', [WebScheduleController::class, 'index']);
});
