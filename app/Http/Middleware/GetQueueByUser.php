<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetQueueByUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find($request->idUser);
        $counter = Counter::where('user_id', $user->id)->first() ?? null;
        if(!$counter) {
            throw new HttpResponseException(response()->json([
                "status" => "Unprocessable Entity",
                'data' => null,
                'error' => [
                    "error_message" => "the user hasn't registered to counter"
                ]
            ], 422));
        }
        return $next($request);
    }
}
