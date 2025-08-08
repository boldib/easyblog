<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagsController;

Auth::routes();

Route::get( '/', [ HomeController::class, 'index' ] )->name( 'home' );

//Comments
Route::group( [ 'middleware' => 'auth' ], function () {
	Route::post( '/comment/{id}', [ CommentsController::class, 'create' ] );
	Route::get( '/comments/edit/{id}', [ CommentsController::class, 'edit' ] );
	Route::patch( '/comments/edit/{id}', [ CommentsController::class, 'update' ] );
	Route::delete( '/comments/delete/{id}', [ CommentsController::class, 'delete' ] );
} );

//Tags
Route::get( '/tag/{slug}', [ TagsController::class, 'index' ] );

//Search
Route::get( 'search', [ SearchController::class, 'index' ] )->name( 'search' );

//Profiles
Route::get( '/{profileslug}', [ ProfilesController::class, 'show' ] );
Route::get( '/profile/edit/{profileslug}', [ ProfilesController::class, 'edit' ] )->middleware( [ 'auth' ] );
Route::patch( '/profile/edit/{profileslug}/save', [ ProfilesController::class, 'update' ] )->middleware( [ 'auth' ] );
Route::delete( '/profile/delete/{id}', [ ProfilesController::class, 'delete' ] )->middleware( [ 'auth' ] );

//Posts
Route::name( 'post.' )->group( function () {
	Route::get( '/{profileslug}/{postslug}', [ PostsController::class, 'show' ] );
	Route::get( '/create/new/post', [ PostsController::class, 'create' ] )->middleware( [ 'auth' ] )->name( 'create' );
	Route::post( '/store', [ PostsController::class, 'store' ] )->middleware( [ 'auth' ] )->name( 'store' );
	Route::get( '/post/edit/{id}', [ PostsController::class, 'edit' ] )->middleware( [ 'auth' ] );
	Route::patch( '/post/update/{id}', [ PostsController::class, 'update' ] )->middleware( [ 'auth' ] );
	Route::delete( '/post/delete/{id}', [ PostsController::class, 'delete' ] )->middleware( [ 'auth' ] );
} );
