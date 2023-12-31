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
        // dd($request->initial);
        if(!empty($request->idService)){
            $currentService = Service::find($request->idService);
            $anotherService = Service::where('initial', $request->initial)->first();
            if($anotherService && $anotherService->initial != $currentService->initial){
                throw new HttpResponseException(response()->json([
                    "status" => "Conflict",
                    "data" => null,
                    "error" => [
                        "error_message" => 'Inisial sudah digunakan di layanan lain'
                    ]
                ], 409));
            }
        }
        if(empty($request->idService)){
            $anotherService = Service::where('initial', $request->initial)->first();
            if($anotherService){
                throw new HttpResponseException(response()->json([
                    "status" => "Conflict",
                    "data" => null,
                    "error" => [
                        "error_message" => 'Inisial sudah digunakan di layanan lain'
                    ]
                ], 409));
            }
        }
        
        return $next($request);
        
    }
}
