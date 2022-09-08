<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostsController;
use App\Http\Controllers\CommentsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

//COMMENTS
Route::group(['middleware' => 'auth'], function () {
    Route::post('/comment/{id}', [CommentsController::class, 'create']);
    Route::get('/comments/edit/{id}',[CommentsController::class, 'edit']);
    Route::patch('/comments/edit/{id}', [CommentsController::class, 'update']);
    Route::delete('/comments/delete/{id}', [CommentsController::class, 'delete']);
});

//Posts
Route::name('post.')->group(function () {
    Route::get('/{profileslug}/{postslug}', [PostsController::class, 'show'])->name('new');
    Route::get('/new', [PostsController::class, 'create'])->name('create');
    Route::post('/store', [PostsController::class, 'store'])->name('store');
    Route::get('/post/edit/{id}',[PostsController::class, 'edit']);
    Route::patch('/post/update/{id}', [PostsController::class, 'update']);
    Route::delete('/psot/delete/{id}', [PostsController::class, 'delete']);
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
