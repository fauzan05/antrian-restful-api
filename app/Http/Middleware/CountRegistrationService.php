<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CountRegistrationService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $services = Service::select('id')->get();
        if (count($services) < 1) {
            throw new HttpResponseException(response()->json([
                "status" => "Not Found Error",
                'data' => null,
                'error' => [
                    "error_message" => "service with registration role is not found"
                ]
            ], 404));
        }
        return $next($request);
    }
}
