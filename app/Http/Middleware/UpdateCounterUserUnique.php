<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCounterUserUnique
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $counter = Counter::find($request->idCounter);
        if($counter->user->id == $request->user_id && $counter->id == $request->idCounter)
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
