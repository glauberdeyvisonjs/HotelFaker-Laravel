<?php

use App\Http\Controllers\CollaboratorsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth.api')->name('app.')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/list', [UserController::class, 'list'])->name('list');
        Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
    });

    Route::prefix('collaborators')->name('collaborators.')->group(function () {
        Route::get('/list', [CollaboratorsController::class, 'list'])->name('list');
        Route::post('/store', [CollaboratorsController::class, 'store'])->name('store');
        Route::get('/show/{id}', [CollaboratorsController::class, 'show'])->name('show');
        Route::post('/delete/{id}', [CollaboratorsController::class, 'destroy'])->name('delete');
    });
});
