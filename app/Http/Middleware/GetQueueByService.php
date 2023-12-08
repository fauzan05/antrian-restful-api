<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetQueueByService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $service = Service::where('id', $request->idService)->first();
        if($service->role == 'registration')
        {
            $queue = Queue::where('registration_service_id', $service->id)->whereIn('registration_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())->orderByDesc('registration_number')->first() ?? null;
            if($queue == null) {
                throw new HttpResponseException(response()->json([
                    "status" => "OK",
                    "data" => null,
                    "error" => null
                ], 404));
            }
        }
        if($service->role == 'poly')
        {
            $queue = Queue::where('poly_service_id', $service->id)->whereIn('poly_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())->orderByDesc('poly_number')->first() ?? null;
            if($queue == null) {
                throw new HttpResponseException(response()->json([
                    "status" => "OK",
                    "data" => null,
                    "error" => null
                ], 404));
            }
        }
        
        return $next($request);
    }
}
