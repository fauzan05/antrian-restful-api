<?php

namespace App\Http\Controllers;

use App\Http\Requests\CounterCreateRequest;
use App\Http\Resources\CounterResource;
use App\Models\Counter;
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
        ])->setStatusCode(200);
    }

    public function show()
    {
        return response()->json([
            'status' => 'OK',
            'data' => CounterResource::collection(Counter::all()),
            'error' => null
        ])->setStatusCode(200);
    }

    public function update(CounterCreateRequest $request, int $idCounter)
    {
        $data = $request->validated();
        $counter = Counter::where('id', $idCounter)->first();
        $counter->name = trim($data['name']);
        $counter->is_active = (boolean)$data['is_active'] ? true : false;
        $counter->fill($data);
        $counter->save();
        $counter = Counter::where('id', $idCounter)->first();
        return response()->json([
            'status' => 'OK',
            'data' => new CounterResource($counter),
            'error' => null
        ])->setStatusCode(200);
    }

    public function delete(int $id)
    {
        Counter::where('id', $id)->delete();
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null
        ])->setStatusCode(200);
    }

    public function destroy()
    {
        DB::delete("delete from counters");
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null
        ])->setStatusCode(200);
    }

    public function currentCounterByUser(int $idUser)
    {
        $counter = Counter::where('user_id', $idUser)->first();
        return response()->json([
            'status' => 'OK',
            'data' => new CounterResource($counter),
            'error' => null
        ])->setStatusCode(200);
    }
}
