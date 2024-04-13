<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceIdIsExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // via parameter
        if ($request->idService) {
            $service = Service::select('id')->find($request->idService);
            if (!$service) {
                throw new HttpResponseException(response()->json([
                    "status" => "Not Found",
                    'data' => null,
                    'error' => [
                        "error_message" => "service not found"
                    ]
                ], 404)); 
            }

        }

        // via body
        if ($request->service_id) {
            $service = Service::select('id')->find($request->service_id);
            if (!$service) {
                throw new HttpResponseException(response()->json([
                    "status" => "Not Found",
                    'data' => null,
                    'error' => [
                        "error_message" => "service not found"
                    ]
                ], 404)); 
            }
        }
        return $next($request);
    }
}
