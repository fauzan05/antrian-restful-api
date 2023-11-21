<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Queue;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CurrentQueue
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queue = Queue::where('service_id', $request->idService)->whereIn('status', ['called', 'skipped'])
        ->whereDate('created_at', Carbon::today())->orderByDesc('number')->get()->all() ?? null;
        if($queue == null) {
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                "data" => null,
                "error" => [
                    "error_message" => 'current queue is not found'
                ]
            ], 404));
        }
        return $next($request);
    }
}
