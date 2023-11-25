<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use App\Models\User;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCounterValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find($request->idUser) ?? null;
        if(!$user)
        {
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                'data' => null,
                'error' => [
                    "error_message" => "user not found"
                ]
            ], 404));
        }
        if($user->role == 'admin')
        {
            throw new HttpResponseException(response()->json([
                "status" => "Bad Request",
                'data' => null,
                'error' => [
                    "error_message" => "user id must has role as operator"
                ]
            ], 400));
        }
        $counter = Counter::where('user_id', $user->id)->first() ?? null;
        if(!$counter && $user->role == 'operator')
        {
            throw new HttpResponseException(response()->json([
                "status" => "Unprocessable Entity",
                'data' => null,
                'error' => [
                    "error_message" => "your account haven't yet registered into counters"
                ]
            ], 422));
        }
        return $next($request);
    }
}
