<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitialServiceValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $service = Service::where('initial', $request->initial)->exists();
        if($service){
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    "error_message" => 'initial service has been used'
                ]
            ], 404));
        } else {
            return $next($request);
        }
    }
}
