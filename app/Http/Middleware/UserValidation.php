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


use function PHPUnit\Framework\isNull;

class UserValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('username', trim($request->username))->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                'data' => null,
                'error' => [
                    "error_message" => "username or password is wrong"
                ]
            ], 401));
        }
        return $next($request);
    }
}
