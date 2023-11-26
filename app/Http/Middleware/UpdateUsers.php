<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(auth()->user()->id);
        if(!$user || !Hash::check($request->old_password, $user->password))
        {
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                'data' => null,
                'error' => [
                    "error_message" => "old password is wrong"
                ]
            ], 401));
        }
        return $next($request);
    }
}
