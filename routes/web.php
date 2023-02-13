<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddleware;

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
Route::get('/', function () {
    return view('welcome');
});


Route::post('/api/register',[App\Http\Controllers\Gener02Controller::class,'register']);
Route::post('/api/login',[App\Http\Controllers\Gener02Controller::class,'login']);

Route::post('/api/registro/validateNomin02',[App\Http\Controllers\RegistroController::class,'validateNomin02']);









