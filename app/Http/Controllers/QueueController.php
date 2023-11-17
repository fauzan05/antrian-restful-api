<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueCreateRequest;
use App\Http\Requests\UpdateQueueRequest;
use App\Http\Resources\CounterResource;
use App\Http\Resources\CounterSimpleResource;
use App\Http\Resources\QueueResource;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QueueController extends Controller
{
    public function create(QueueCreateRequest $request)
    {
        $data = $request->validated(); 
        $number = Queue::where('service_id', $data['service_id'])
        ->whereDate('created_at', Carbon::today())->count();
        $service = Service::where('id', $data['service_id'])->first();
        $queue = new Queue();
        $queue->number = $service->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $data['service_id'];
        $queue->status = 'waiting';
        $queue->save();
        return response()->json([
            'status' => "OK",
            'data' => [
                new QueueResource($queue)
            ],
            'error' => 'null'
        ])->setStatusCode(201);   
    }

    public function get(int $id)
    {
        $queue = Queue::where('id', $id)->first();
        
        $counter = Counter::where('id', $queue->service['counter_id'])->first();
        return response()->json([
            'status' => 'OK',
            'data' => 
                new QueueResource($queue),
                new CounterResource($counter)
            ,
            'error' => null
        ]);
    }

    public function show()
    {
        return response()->json([
            'status' => 'OK',
            'data' =>  QueueResource::collection(Queue::all()),
            'error' => null
        ]);
    }

    public function update(int $idQueue, UpdateQueueRequest $request)
    {
        $data = $request->validated();
        $queue = Queue::where("id", $idQueue)
        ->whereDate("created_at", Carbon::today())->update([
            'status' => $data['status']
        ]);
        $queue = Queue::where("id", $idQueue)
        ->whereDate("created_at", Carbon::today())->get();
        return response()->json([
            'status' => 'OK',
            'data' => QueueResource::collection($queue),
            'error' => null
        ]);
    }

    public function count(int $idService)
    {
        $queue = Queue::where('service_id', $idService)->whereIn('status', ['called', 'skipped'])
        ->whereDate('created_at', Carbon::today())->orderByDesc('number')->get()->all();
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue[0]),
            'error' => null
        ]);
    }


    public function destroy()
    {
        DB::delete("delete from queues");
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null
        ]);
    }

   

}
