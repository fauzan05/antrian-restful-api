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
        $queue = Queue::find($request->idQueue);
        $counter = Counter::where('id', $request->counter_id)
                    ->where('service_id', $queue->service_id)->first();
        if($counter == null){
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                'data' => null,
                'error' => [
                    "error_message" => "the counter used to serve the service is not suitable "
                ]
            ], 401));
        }else {
            return $next($request);
        }
    }
}
