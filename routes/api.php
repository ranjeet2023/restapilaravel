<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;

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


Route::controller(PostController::class)->group(function(){
    Route::post('login','loginUser');
});

Route::controller(PostController::class)->group(function(){
    Route::get('posts', 'index');
    Route::post('post', 'store');
    Route::get('show/{id}', 'show');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'destroy');
    Route::post('logout','logout');
    Route::get('user','getUserDetail');
    Route::get('logout','userLogout');
})->middleware('auth:api');
