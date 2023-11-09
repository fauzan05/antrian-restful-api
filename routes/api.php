<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/users/register', [AuthController::class,'register']);
Route::post('/users/login', [AuthController::class,'login']);
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/users', [AuthController::class,'get']);
    Route::get('/users/current', [AuthController::class,'currentUser']);
    Route::put('/users/update', [AuthController::class,'update']);
    Route::delete('/users/logout', [AuthController::class,'logout']);
    Route::delete('/users/{idUser}', [AuthController::class,'delete'])->where('idUser', '[0-9]+');
});

   




