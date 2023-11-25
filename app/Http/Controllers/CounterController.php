<?php

namespace App\Http\Controllers;

use App\Http\Requests\CounterCreateRequest;
use App\Http\Requests\CounterUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\CounterResource;
use App\Http\Resources\CurrentQueueResource;
use App\Http\Resources\UserCollection;
use App\Models\Counter;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function create(CounterCreateRequest $request)
    {
        $data = $request->validated();
        $counter = new Counter();
        $counter->name = trim($data['name']);
        $counter->fill($data);
        $counter->save();

        return response()->json([
            'status' => "OK",
            'data' => new CounterResource($counter),
            'error' => 'null'
        ])->setStatusCode(201);
    }

    public function get(int $idCounter)
    {
        $counter = Counter::find($idCounter);
        return response()->json([
            'status' => 'OK',
            'data' => new CounterResource($counter),
            'error' => null
        ]);
    }

    public function show()
    {
        return response()->json([
            'status' => 'OK',
            'data' => CounterResource::collection(Counter::where('is_active', true)->get()),
            'error' => null
        ]);
    }

    public function update(CounterCreateRequest $request, int $idCounter)
    {
        $counter = Counter::where('id', $idCounter)->first();
        $data = $request->validated();
        $counter->name = trim($data['name']);
        $counter->fill($data);
        $counter->save();
        $counter = Counter::where('id', $idCounter)->first();
        return response()->json([
            'status' => 'OK',
            'data' => new CounterResource($counter),
            'error' => null
        ]);
    }

    public function delete(int $id)
    {
        Counter::where('id', $id)->delete();
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null
        ]);
    }

    public function destroy()
    {
        DB::delete("delete from counters");
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null
        ]);
    }

    public function currentQueueByCounter()
    {
        $counters = Counter::all();
        $queue = [];
        foreach ($counters as $counter) {
            $result = DB::table('counters')
                ->join('services', 'counters.service_id', '=', 'services.id')
                ->join('queues', 'services.id', '=', 'queues.service_id')
                ->select('counters.name', 'queues.number')
                ->where('counters.id', '=', $counter->id)
                ->whereIn('queues.status', ['called', 'skipped'])
                ->orderBy('queues.number', 'desc')
                ->first();
                if($result == null)
                {
                    $queue[] = [
                        'name' => $counter->name,
                        'number' => 0
                    ];
                }else{
                    $queue[] = $result;
                }
        }
        return response()->json([
            'status' => 'OK',
            'data' => new CurrentQueueResource($queue),
            'error' => null
        ]);
    }
}
