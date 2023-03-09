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

Route::post('api/horarios/adicionar', [App\Http\Controllers\HorariosController::class,'adicionar']);
Route::post('api/horarios/editar', [App\Http\Controllers\HorariosController::class,'editar']);
Route::post('api/horarios/eliminar', [App\Http\Controllers\HorariosController::class,'eliminar']);


Route::post('api/tipo_horarios/TipoHorario', [App\Http\Controllers\Tipo_horarioController::class,'TipoHorario']);
Route::post('api/horarios/saveHorario', [App\Http\Controllers\HorariosController::class,'saveHorario']);

Route::post('api/horarios/getConta28', [App\Http\Controllers\HorariosController::class,'getConta28']);
Route::post('api/horarios/getHorarios', [App\Http\Controllers\HorariosController::class,'getHorarios']);


Route::get('/api/trabajador_horario/searchConta28',[App\Http\Controllers\Trabajador_horarioController::class,'searchConta28']);
Route::post('/api/nomin02/getNomin02',[App\Http\Controllers\Nomin02Controller::class,'getNomin02']);

Route::post('/api/trabajador_horario/guardarTrabajadorHorario',[App\Http\Controllers\Trabajador_horarioController::class,'guardarTrabajadorHorario']);

Route::post('/api/nomin02/traerUltimo',[App\Http\Controllers\Nomin02Controller::class,'traerUltimo']);


Route::post('/api/registro/permisos',[App\Http\Controllers\RegistroController::class,'permisos']);


Route::get('/api/horario/searchHorario',[App\Http\Controllers\HorariosController::class,'searchHorario']);

Route::get('/api/gener02/searchGener02',[App\Http\Controllers\Gener02Controller::class,'searchGener02']);



