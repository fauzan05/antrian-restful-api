<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(User::where('username', $request->input('username'))->exists()) {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "error_message" => 'username has been already registered'
            ], 400));
        }else {
            return $next($request);
        }
    }
}
