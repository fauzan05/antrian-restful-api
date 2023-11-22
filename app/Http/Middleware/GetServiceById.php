<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

use function PHPUnit\Framework\isNull;

class GetServiceById
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $service = Service::find($request->idService) ?? null;
        if(isNull($service)){
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                "data" => null,
                "error" => [
                    "error_message" => 'Service is not found'
                ]
            ], 404));
        }
        return $next($request);
    }
}
