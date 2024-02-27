<?php

namespace App\Http\Middleware;

use App\Models\OperationalHours;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckOperationalHours
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $operationalHours = OperationalHours::all();
        $currentDay = now()->translatedFormat('l');
        foreach($operationalHours as $key => $oh):
            if($currentDay === $oh->days):
            //     $now = Carbon::now();
            // dd($now->greaterThan($oh->open));
                // dd($oh->days);
                if($oh->close && (boolean)$oh->is_active && now()->greaterThan($oh->close)) {
                    throw new HttpResponseException(response()->json([
                        "status" => "Forbidden",
                        "data" => null,
                        "error" => [
                            "error_message" => "Puskesmas sudah tutup!"
                        ]
                    ], 403));
                    // dd($oh->close);
                }
                if($oh->open && (boolean)$oh->is_active && now()->lessThan($oh->open)) {
                    throw new HttpResponseException(response()->json([
                        "status" => "Forbidden",
                        "data" => null,
                        "error" => [
                            "error_message" => "Puskesmas belum dibuka!"
                        ]
                    ], 403));
                }
                
            endif;
            
        endforeach;
        return $next($request);
    }
}
