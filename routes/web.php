<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('posts')->group(function () {
        Route::get('/create', function() {
            return view('posts.create');
        })->name('posts.create.ui');
        Route::get('/list', function() {
            return view('posts.list');
        })->name('posts.list.ui');
        Route::get('/details/{id}', function(string $id) {
            return view('posts.details', ['id' => $id]);
        })->name('posts.details.ui');
        Route::get('/update/{id}', function(string $id) {
            return view('posts.update', ['id' => $id]);
        })->name('posts.update.ui');
    });

    Route::prefix('comments')->group(function () {
        Route::get('/list', function() {
            return view('comments.list');
        })->name('comments.list.ui');
        Route::get('/update/{id}', function(string $id) {
            return view('comments.update', ['id' => $id]);
        })->name('comments.update.ui');;
    });
});

require __DIR__.'/auth.php';
