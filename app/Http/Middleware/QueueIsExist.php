<?php

namespace App\Http\Middleware;

use App\Models\Queue;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QueueIsExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Queue::where("id", $request->idQueue)->exists()) {
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    "error_message" => "queue is not found"
                ]
            ], 404));
        } else {
            return $next($request);
        }
    }
}
