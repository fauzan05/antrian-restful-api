<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCounterById
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Counter::find($request->idCounter)){
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                "data" => null,
                "error" => [
                    "error_message" => 'counter is not found'
                ]
            ], 404));
        }else{
            return $next($request);
        }
    }
}
