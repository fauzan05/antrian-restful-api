<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCounterNameUnique
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $counter1 = Counter::find($request->idCounter); //current counter
        $counter2 = Counter::where('name', $request->name)->first(); //find another counter with request name
        if(!$counter2)
        {
            return $next($request);
        }
        if($counter2->name != $counter1->name)
        {
            throw new HttpResponseException(response()->json([
                "status" => "Conflict",
                "data" => null,
                "error" => [
                    "error_message" => 'Nama loket sudah digunakan'
                ]
            ], 409));
        }
        return $next($request);

    }
}
