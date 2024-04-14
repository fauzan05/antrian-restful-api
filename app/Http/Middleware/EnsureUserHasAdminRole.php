<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if(($user->hasRole() == 'operator')) {
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    "error_message" => "Access Denied! this action must be admin role"
                ]
            ], 401));
        }else{
            return $next($request);
        }
    }
}
