<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\SubscribersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Home Page
Route::get('/home', [BlogController::class, 'blogListing'])->name('blogs.blog-home-page');
Route::get('/', [BlogController::class, 'blogListing'])->name('blogs.blog-home-page');
Route::get('/blog-detail-page/{id}', [BlogController::class, 'blogDetailPage'])->name('blogs.blog-detail-page');

// laravel default Auth scaffolding
Auth::routes();

Route::group(['middleware' => ['auth']], static function() {
    // Subscribers routes
    Route::get('subscribers', [SubscribersController::class, 'index'])->name('subscribers.index');
    Route::resource('subscribers', SubscribersController::class);

    // Blogs routes
    Route::get('/blogs', [BlogController::class, 'blogListing'])->name('blogs.index');
    Route::resource('blogs', BlogController::class);
});
