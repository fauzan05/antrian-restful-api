<?php

namespace App\Http\Middleware;

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
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        if(!in_array($request->days, $days))
        {
            throw new HttpResponseException(
                response()->json(
                    [
                        'status' => 'Bad Request',
                        'data' => null,
                        'error' => [
                            'error_message' => 'Day is not valid',
                        ],
                    ],
                    400,
                ),
            );
        }
        return $next($request);
    }
}
