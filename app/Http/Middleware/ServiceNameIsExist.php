<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceNameIsExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!empty($request->idService)){
            $currentService = Service::find($request->idService);
            $anotherService = Service::where('name', 'like', '%'. $request->name .'%')->first();
            if($anotherService && $anotherService->name != $currentService->name){
                throw new HttpResponseException(response()->json([
                    "status" => "Conflict",
                    'data' => null,
                    'error' => [
                        "error_message" => "nama layanan sudah ada"
                    ]
                ], 409)); 
            }
        }

        if(empty($request->idService)){
            $anotherService = Service::where('name', 'like', '%'. $request->name .'%')->first();
            if(!empty($anotherService)){
                throw new HttpResponseException(response()->json([
                    "status" => "Conflict",
                    'data' => null,
                    'error' => [
                        "error_message" => "nama layanan sudah ada"
                    ]
                ], 409)); 
            }
        }
        
        return $next($request);
    }
}
