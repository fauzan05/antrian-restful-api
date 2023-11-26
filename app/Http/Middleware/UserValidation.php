<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('username', trim($request->username))->first() ?? null;
        if(!$user || !Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                'data' => null,
                'error' => [
                    "error_message" => "username or password is wrong"
                ]
            ], 401));
        }
        $counter = Counter::where('user_id', $user->id)->first() ?? null;
        if(!$counter && $user->role == 'operator') {
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
