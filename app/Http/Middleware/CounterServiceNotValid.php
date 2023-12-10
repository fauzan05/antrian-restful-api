<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use App\Models\Queue;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CounterServiceNotValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (isset($request->counter_id) && isset($request->idQueue)) {
            $counter = Counter::where('id', $request->counter_id)->first() ?? null;
            // dd($counter->service);
            if (!$counter) {
                throw new HttpResponseException(response()->json([
                    "status" => "Not Found",
                    'data' => null,
                    'error' => [
                        "error_message" => "counter not found"
                    ]
                ], 404));
            }
            if (!$counter->service) {
                throw new HttpResponseException(response()->json([
                    "status" => "Validation Error",
                    'data' => null,
                    'error' => [
                        "error_message" => "the counter has not registered into services"
                    ]
                ], 401));
            }
            if (!$counter->service->role) {
                $queue = Queue::where('id', $request->idQueue)->where('poly_service_id', $counter->service->id)->first() ?? null;
                if (!$queue) {
                    throw new HttpResponseException(response()->json([
                        "status" => "Validation Error",
                        'data' => null,
                        'error' => [
                            "error_message" => "the counter has not registered into services"
                        ]
                    ], 401));
                }
            }
            if ($counter->service->role == 'poly') {
                $queue = Queue::where('id', $request->idQueue)->where('poly_service_id', $counter->service->id)->first() ?? null;
                if (!$queue) {
                    throw new HttpResponseException(response()->json([
                        "status" => "Validation Error",
                        'data' => null,
                        'error' => [
                            "error_message" => "the service counter has not match with poly service id" // yang bisa mengubah poly_service_id adalah counter yang memiliki service id yang sama dengan poly_service_id (service yang terkait)
                        ]
                    ], 401));
                }
            }
            
        }
        if (isset($request->idUser)) {
            $counter = Counter::where('user_id', $request->idUser)->first() ?? null;
            if (!$counter->service) {
                throw new HttpResponseException(response()->json([
                    "status" => "Validation Error",
                    'data' => null,
                    'error' => [
                        "error_message" => "the counter has not registered into services"
                    ]
                ], 401));
            }
        }
        return $next($request);
    }
}
