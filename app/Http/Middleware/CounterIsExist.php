<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CounterIsExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Counter::where('id', $request->idCounter)
        ->where('user_id', $request->idUser)->exists()){
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
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
