<?php

use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ServiceController;
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

// users
Route::post('/users/register', [AuthController::class, 'register'])
    ->middleware('username');
Route::post('/users/login', [AuthController::class, 'login'])->middleware(['userValidation']);

// harus login
Route::group(['middleware' => ['auth:sanctum']], function () {
    // users
    Route::get('/users/current', [AuthController::class, 'get']);
    Route::put('/users/update-password', [AuthController::class, 'updateCurrent'])
        ->middleware('updateUsers');
    Route::delete('/users/logout', [AuthController::class, 'logout']);
    Route::delete('/users/{idUser}', [AuthController::class, 'delete'])
        ->where('idUser', '[0-9]+')
        ->middleware('isAdmin');

    // harus admin
    Route::middleware('isAdmin')->group(function () {
        Route::put('/users/{idUser}', [AuthController::class, 'update'])
            ->middleware('updateUsers')
            ->where('idUser', '[0-9]+');
        
        // services
        Route::post('/services', [ServiceController::class, 'create'])->middleware(['serviceNameIsExist', 'initialIsExist']);
        Route::put('/services/{idService}', [ServiceController::class, 'update'])
            ->middleware(['serviceIdIsExist','serviceNameIsExist', 'initialIsExist'])
            ->where('idService', '[0-9]+');
        Route::delete('/services/{idService}', [ServiceController::class, 'delete'])
            ->where('idService', '[0-9]+');
        Route::delete('/services', [ServiceController::class, 'destroy']);

        // counters
        Route::post('/counters', [CounterController::class, 'create'])
            ->middleware(['serviceIdIsExist','createCounterUserUnique', 'createCounterNameUnique']);
        Route::put('/counters/{idCounter}', [CounterController::class, 'update'])
            ->where('idCounter', '[0-9]+')
            ->middleware(['getCounterById','updateCounterUserUnique','updateCounterNameUnique']);
        Route::delete('/counters/{idCounter}', [CounterController::class, 'delete'])
            ->where('idCounter', '[0-9]+')
            ->middleware('getCounterById');
        Route::delete('/counters', [CounterController::class, 'destroy']);

        // admin settings
        Route::put('/app/operational-hours', [AppSettingController::class, 'setOperationalHours']);
        Route::delete('/app/operational-hours', [AppSettingController::class, 'deleteAllOperationalHours']);
        Route::put('/app/identity', [AppSettingController::class, 'setIdentityOfInstitute']);
        Route::put('/app/text-footer', [AppSettingController::class, 'setTextFooterDisplay']);
        Route::put('/app/color-footer', [AppSettingController::class, 'setColorFooterDisplay']);
        
        // files
        Route::delete('/files/videos/{filename}', [FileController::class, 'deleteVideo'])->middleware('checkVideoFiles');
    });

    // queues
    Route::put('/queues/{idQueue}', [QueueController::class, 'update'])
        ->where('idQueue', '[0-9]+')->middleware(['getQueueById', 'counterServiceNotValid']);
});

// services
Route::get('/services', [ServiceController::class, 'show']);
Route::get('/services/{idService}', [ServiceController::class, 'get'])
    ->where('idService', '[0-9]+')
    ->middleware('getServiceById');

// queues
Route::post('/queues', [QueueController::class, 'create'])->middleware(['checkOperationalHours', 'countRegistrationService']);
Route::get('/queues/count', [QueueController::class, 'countAllQueue']);
Route::get('/queues', [QueueController::class, 'show']);
Route::get('/queues/{idQueue}', [QueueController::class, 'get'])
    ->where('idQueue', '[0-9]+')->middleware('getQueueById');
Route::get('/queues/counters/{idCounter}', [QueueController::class, 'showQueueByCounter'])
    ->where('idCounter', '[0-9]+')->middleware('getCounterById');
Route::get('/queues/counters/{idCounter}/current-queue', [QueueController::class, 'allCurrentQueueByCounter']);
Route::get('/queues/users/{idUser}/current-queue', [QueueController::class, 'allCurrentQueueByUser']);
Route::get('/queues/counters/current-queue', [QueueController::class, 'allCurrentQueue']);
Route::get('/queues/users/{idUser}', [QueueController::class, 'showQueueByUser'])
    ->where('idUser', '[0-9]+')->middleware(['getQueueByUser', 'counterServiceNotValid']);
Route::get('/queues/services/{idService}/current', [QueueController::class, 'currentByService'])
    ->where('idService', '[0-9]+')->middleware('getQueueByService');
Route::get('/queues/counters/{idCounter}/current', [QueueController::class, 'currentByCounter'])
    ->where('idCounter', '[0-9]+')->middleware('getQueueByCounter');
Route::get('/counters/users/{idUser}', [CounterController::class, 'currentCounterByUser'])
    ->where('idUser', '[0-9]+')->middleware('userCounterValidation');
Route::delete('/queues', [QueueController::class, 'destroy']);

// users
Route::get('/users', [AuthController::class, 'show']);

// counters
Route::get('/counters/{idCounter}', [CounterController::class, 'get'])
    ->where('idCounter', '[0-9]+')
    ->middleware('getCounterById');
Route::get('/counters', [CounterController::class, 'show']);

// files
Route::get('/files/audios', [FileController::class, 'showAllAudios'])->middleware('checkAudioFiles');
Route::get('/files/audios/{filename}', [FileController::class, 'getAudio'])->middleware('checkAudioFiles');
Route::get('/files/videos', [FileController::class, 'showAllVideos'])->middleware('checkVideoFiles');
Route::post('/files/videos', [FileController::class, 'uploadedVideo']);
Route::delete('/files/videos', [FileController::class, 'deleteAllVideo']);
Route::get('/files/videos/selected', [FileController::class, 'getVideo']);

// app settings
Route::get('/app/selected-video', [AppSettingController::class, 'getSelectedVideo']);
Route::post('/app/videos', [AppSettingController::class, 'setVideoDisplay']);
Route::get('/app/operational-hours', [AppSettingController::class, 'showOperationalHours']);
Route::get('/app', [AppSettingController::class, 'showAllSettings']);
