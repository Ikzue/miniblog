<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Models\Post;
use App\Models\Comment;
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
    return view('posts.list');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('posts')->group(function () {
        Route::get('/create', fn () => view('posts.create'))->name('posts.create.ui');
        Route::get('/list', fn () => view('posts.list'))->name('posts.list.ui');
        Route::get('/details/{post}', fn (Post $post) => view('posts.details', ['post' => $post]))->name('posts.details.ui');
        Route::get('/update/{post}', fn (Post $post) => view('posts.update', ['post' => $post]))->name('posts.update.ui');
    });

    Route::prefix('comments')->group(function () {
        Route::get('/list', fn () => view('comments.list'))->name('comments.list.ui');
        Route::get('/update/{id}', fn (Comment $comment) => view('comments.update', ['comment' => $comment]))->name('comments.update.ui');
    });
});

require __DIR__.'/auth.php';
