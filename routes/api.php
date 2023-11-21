<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ServiceController;
use App\Http\Middleware\EnsureUserHasAdminRole;
use App\Models\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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


Route::post('/users/register', [AuthController::class,'register'])
->middleware('username');
Route::post('/users/login', [AuthController::class,'login']);
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/users', [AuthController::class,'show'])->middleware('isAdmin');
    Route::get('/users/current', [AuthController::class,'get']);
    Route::put('/users/update', [AuthController::class,'update']);
    Route::delete('/users/logout', [AuthController::class,'logout']);
    Route::delete('/users/{idUser}', [AuthController::class,'delete'])
    ->where('idUser', '[0-9]+')
    ->middleware('isAdmin');
    
    Route::middleware('isAdmin')->group(function() {
        Route::post('/services', [ServiceController::class,'create'])->middleware('initialIsExist');
        Route::put('/services/{idService}', [ServiceController::class,'update'])
        ->where('idService', '[0-9]+');
        Route::delete('/services/{idService}', [ServiceController::class,'delete'])
        ->where('idService', '[0-9]+');
        Route::delete('/services', [ServiceController::class,'destroy']);
        Route::get('/services/{idService}', [ServiceController::class,'get'])
        ->where('idService', '[0-9]+');

        Route::post('/users/{idUser}/counters', [CounterController::class,'create']);
        Route::put('/users/{idUser}/counters/{idCounter}', [CounterController::class,'update'])
        ->where('idCounter', '[0-9]+')->where('idUser', '[0-9]+');
        Route::delete('/counters/{idCounter}', [CounterController::class,'delete'])
        ->where('idCounter', '[0-9]+');
        Route::delete('/counters', [CounterController::class,'destroy']);
    });
    Route::get('/services', [ServiceController::class,'show']);
    Route::get('/users/{idUser}/counters', [CounterController::class,'get'])
    ->where('idCounter', '[0-9]+')->where('idUser', '[0-9]+')
    ->middleware('counterIsExist');
    Route::get('/counters', [CounterController::class,'show']);
});

    Route::post('/queues', [QueueController::class, 'create']);
    Route::get('/queues', [QueueController::class, 'show']);
    Route::get('/queues/{idQueue}', [QueueController::class, 'get'])
    ->where('idQueue', '[0-9]+')->middleware('queueIsExist');
    Route::get('/services/{idService}/queue-count', [QueueController::class, 'count'])
    ->where('idService', '[0-9]+')->middleware('currentQueue');
    Route::put('/queues/{idQueue}', [QueueController::class, 'update'])
    ->where('idQueue', '[0-9]+')->middleware('queueIsExist');
    Route::delete('/queues', [QueueController::class,'destroy']);
    Route::get('/counters/current-queue', [CounterController::class, 'currentQueue']);
    Route::get('/files', [FileController::class, 'index'])->middleware('checkFiles');
    Route::get('/files/{nameFile}', [FileController::class, 'get'])->middleware('checkFiles');


    