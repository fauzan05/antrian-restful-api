<?php

use App\Events\TestEvent;
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
Route::post('/users/login', [AuthController::class,'login'])->middleware(['userValidation']);
// harus login
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/users/current', [AuthController::class,'get']);
    Route::put('/users/update', [AuthController::class,'update'])
    ->middleware('updateUsers');
    Route::delete('/users/logout', [AuthController::class,'logout']);
    Route::delete('/users/{idUser}', [AuthController::class,'delete'])
    ->where('idUser', '[0-9]+')
    ->middleware('isAdmin');
    

    Route::middleware('isAdmin')->group(function() {
        Route::get('/users', [AuthController::class,'show']);
        Route::post('/services', [ServiceController::class,'create'])->middleware('initialIsExist');
        Route::put('/services/{idService}', [ServiceController::class,'update'])
        ->where('idService', '[0-9]+');
        Route::delete('/services/{idService}', [ServiceController::class,'delete'])
        ->where('idService', '[0-9]+');
        Route::delete('/services', [ServiceController::class,'destroy']);
        Route::get('/services/{idService}', [ServiceController::class,'get'])
        ->where('idService', '[0-9]+')
        ->middleware('getServiceById');

        Route::post('/counters', [CounterController::class,'create']);
        Route::put('/counters/{idCounter}', [CounterController::class,'update'])
        ->where('idCounter', '[0-9]+')
        ->middleware('getCounterById');
        Route::delete('/counters/{idCounter}', [CounterController::class,'delete'])
        ->where('idCounter', '[0-9]+')
        ->middleware('getCounterById');
        Route::delete('/counters', [CounterController::class,'destroy']);
    });
    Route::get('/counters/{idCounter}', [CounterController::class,'get'])
    ->where('idCounter', '[0-9]+')
    ->middleware('getCounterById');
    Route::get('/counters', [CounterController::class,'show']);
    Route::put('/queues/{idQueue}', [QueueController::class, 'update'])
    ->where('idQueue', '[0-9]+')->middleware(['getQueueById','counterServiceNotValid']);
});

    Route::get('/services', [ServiceController::class,'show']);
    Route::post('/queues', [QueueController::class, 'create']);
    Route::get('/queues', [QueueController::class, 'show']);
    Route::get('/queues/{idQueue}', [QueueController::class, 'get'])
    ->where('idQueue', '[0-9]+')->middleware('getQueueById');
    Route::get('/queues/counters/{idCounter}', [QueueController::class, 'showQueueByCounter'])
    ->where('idCounter', '[0-9]+')->middleware('getCounterById');
    Route::get('/queues/counters/{idCounter}/current-queue', [QueueController::class, 'allCurrentQueueByCounter']);
    Route::get('/queues/users/{idUser}/current-queue', [QueueController::class, 'allCurrentQueueByUser']);
    Route::get('/queues/counters/current-queue', [QueueController::class, 'allCurrentQueue']);
    Route::get('/queues/users/{idUser}', [QueueController::class, 'showQueueByUser'])
    ->where('idUser', '[0-9]+')->middleware(['getQueueByUser','counterServiceNotValid']);
    Route::get('/queues/services/{idService}/current', [QueueController::class, 'currentByService'])
    ->where('idService', '[0-9]+')->middleware('getQueueByService');
    Route::get('/queues/counters/{idCounter}/current', [QueueController::class, 'currentByCounter'])
    ->where('idCounter', '[0-9]+')->middleware('getQueueByCounter');
    Route::get('/counters/users/{idUser}', [CounterController::class, 'currentCounterByUser'])
    ->where('idUser', '[0-9]+')->middleware('userCounterValidation');
    Route::delete('/queues', [QueueController::class,'destroy']);
    Route::get('/files', [FileController::class, 'index'])->middleware('checkFiles');
    Route::get('/files/{nameFile}', [FileController::class, 'get'])->middleware('checkFiles');

    