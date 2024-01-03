<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCounterUserUnique
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $counter = Counter::where('user_id', $request->user_id)->first();
        if($counter)
        {
            throw new HttpResponseException(response()->json([
                "status" => "Conflict",
                "data" => null,
                "error" => [
                    "error_message" => 'Operator sudah ada di loket lain'
                ]
            ], 409));
        }
        return $next($request);
    }
}
