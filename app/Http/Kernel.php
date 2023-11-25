<?php

namespace App\Http;

use App\Http\Middleware\CheckAudioFiles;
use App\Http\Middleware\CounterIsExist;
use App\Http\Middleware\CounterServiceNotValid;
use App\Http\Middleware\CounterValidation;
use App\Http\Middleware\CurrentQueue;
use App\Http\Middleware\CurrentQueueIsNull;
use App\Http\Middleware\EnsureUserHasAdminRole;
use App\Http\Middleware\GetCounterById;
use App\Http\Middleware\GetQueueByCounter;
use App\Http\Middleware\GetQueueByService;
use App\Http\Middleware\GetQueueByUser;
use App\Http\Middleware\GetServiceById;
use App\Http\Middleware\HasCounter;
use App\Http\Middleware\InitialServiceValidation;
use App\Http\Middleware\QueueIsExist;
use App\Http\Middleware\RegisterValidation;
use App\Http\Middleware\UserValidation;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAdmin' => EnsureUserHasAdminRole::class,
        'username' => RegisterValidation::class,
        'getCounterById' => GetCounterById::class,
        'initialIsExist' => InitialServiceValidation::class,
        'getQueueById' => QueueIsExist::class,
        'checkFiles' => CheckAudioFiles::class,
        'getQueueByService' => GetQueueByService::class,
        'getQueueByCounter' => GetQueueByCounter::class,
        'getQueueByUser' => GetQueueByUser::class,
        'getServiceById' => GetServiceById::class,
        'userValidation' => UserValidation::class,
        'counterServiceNotValid' => CounterServiceNotValid::class,
    ];
}
