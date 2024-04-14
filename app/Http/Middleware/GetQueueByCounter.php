<?php

namespace App\Http\Middleware;

use App\Models\Counter;
use App\Models\Queue;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetQueueByCounter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $counter = Counter::find($request->idCounter) ?? null;
        if(!$counter)
        {
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                "data" => null,
                "error" => "Counter Not Found"
            ], 404));
        }
        if($counter->service->role == 'registration')
        {
            $queue = Queue::where('registration_service_id', $counter->service->id)->whereIn('registration_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())->orderByDesc('created_at')->get() ?? null;
            if(!$queue) {
                throw new HttpResponseException(response()->json([
                    "status" => "OK",
                    "data" => null,
                    "error" => null
                ], 404));
            }
        }
        if($counter->service->role == 'poly')
        {
            $queue = Queue::where('poly_service_id', $counter->service->id)->whereIn('poly_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())->orderByDesc('created_at')->get() ?? null;
            if($queue == null) {
                throw new HttpResponseException(response()->json([
                    "status" => "OK",
                    "data" => null,
                    "error" => null
                ], 404));
            }
        }
        
        return $next($request);    }
}
