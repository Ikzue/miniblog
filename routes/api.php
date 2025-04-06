<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('posts/getUserPosts/{user}', [PostController::class, 'getUserPosts']);
Route::resource('posts', PostController::class)->middleware('auth')->except(['create', 'edit']);
Route::get('comments/getUserComments/{user}', [CommentController::class, 'getUserComments']);
Route::resource('comments', CommentController::class)->middleware('auth')->except(['create', 'edit']);
Route::resource('users', UserController::class)->middleware('auth')->except(['create', 'edit']);
