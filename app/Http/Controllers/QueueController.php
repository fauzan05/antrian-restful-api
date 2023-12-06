<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueCreateRequest;
use App\Http\Requests\UpdateQueueRequest;
use App\Http\Resources\QueueResource;
use App\Http\Resources\ShowQueueResource;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public function create(QueueCreateRequest $request)
    {
        $data = $request->validated();
        $number = Queue::where('service_id', $data['service_id'])
            ->whereDate('created_at', Carbon::today())
            ->count();
        $service = Service::where('id', $data['service_id'])->first();
        $queue = new Queue();
        $queue->number = $service->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $data['service_id'];https://desktop.postman.com/?desktopVersion=10.20.0&userId=30392518&teamId=0
        $queue->status = 'waiting';
        $queue->save();
        return response()
            ->json([
                'status' => 'OK',
                'data' => new QueueResource($queue),
                'error' => 'null',
            ])
            ->setStatusCode(201);
    }

    public function get(int $id)
    {
        $queue = Queue::where('id', $id)->first();
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue),
            'error' => null,
        ]);
    }

    public function show()
    {
        return response()->json([
            'status' => 'OK',
            'data' => QueueResource::collection(Queue::all()),
            'error' => null,
        ]);
    }

    public function showQueueByCounter(int $idCounter)
    {
        $currentQueue = DB::table('services')
            ->join('queues', 'services.id', '=', 'queues.service_id')
            ->join('counters', 'counters.service_id', '=', 'services.id')
            ->select('queues.number', DB::raw('services.name as service_name'), 'queues.status', DB::raw('counters.name as counters_name'))
            ->where('counters.id', $idCounter)
            ->orderBy('number')
            ->get();

        return response()->json([
            'status' => 'OK',
            'data' => new ShowQueueResource($currentQueue),
            'error' => null,
        ]);
    }

    public function showQueueByUser(int $idUser)
    {
        $currentQueue = DB::table('services')
            ->join('queues', 'services.id', '=', 'queues.service_id')
            ->join('counters', 'counters.service_id', '=', 'services.id')
            ->select('queues.id', 'queues.number', DB::raw('services.name as service_name'), 'queues.status', DB::raw('counters.name as counters_name'))
            ->where('counters.user_id', $idUser)
            ->whereDate('queues.created_at', Carbon::today())
            ->orderBy('queues.number')
            ->get() ?? null;
        if ($currentQueue->isEmpty()) {
            return response()->json([
                'status' => 'OK',
                'data' => [],
                'error' => null,
            ]);
        }
        return response()->json([
            'status' => 'OK',
            'data' => new ShowQueueResource($currentQueue),
            'error' => null,
        ]);
    }

    public function update(int $idQueue, UpdateQueueRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function() use (&$idQueue, &$data){
            Queue::where('id', $idQueue)
            ->whereDate('created_at', Carbon::today())
            ->update([
                'status' => $data['status'],
                'counter_id' => trim($data['counter_id']),
            ]);
        }, 5);
        $queue = Queue::where('id', $idQueue)
            ->whereDate('created_at', Carbon::today())
            ->get();
        return response()->json([
            'status' => 'OK',
            'data' => QueueResource::collection($queue),
            'error' => null,
        ]);
    }

    public function currentByService(int $idService)
    {
        $queue = Queue::where('service_id', $idService)
            ->whereIn('status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('number')
            ->first();
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue),
            'error' => null,
        ]);
    }

    public function currentByCounter(int $idCounter)
    {
        $counter = Counter::find($idCounter);
        $queue = Queue::where('service_id', $counter->service->id)
            ->whereIn('status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('number')
            ->first();
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue),
            'error' => null,
        ]);
    }

    public function destroy()
    {
        DB::delete('delete from queues');
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null,
        ]);
    }
}
