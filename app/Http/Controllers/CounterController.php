<?php

namespace App\Http\Controllers;

use App\Http\Requests\CounterCreateRequest;
use App\Http\Requests\CounterUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\CounterResource;
use App\Http\Resources\UserCollection;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function create(CounterCreateRequest $request, int $id)
    {
        $data = $request->validated();
        $counter = new Counter();
        $counter->name = trim($data['name']);
        $counter->user_id = $id;
        $counter->save();
        
        return response()->json([
            'status' => "OK",
            'data' => new CounterResource($counter),
            'error' => 'null'
        ])->setStatusCode(201);
    }

    public function get(int $idUser, int $idCounter)
    {
        $counter = Counter::where('id', $idCounter)->where('user_id', $idUser)->first();
        return response()->json([
            'status'=> 'OK',
            'data' => new CounterResource($counter),
            'error' => null
        ]);
    }

    public function show()
    {
        return response()->json([
            'status' => 'OK',
            'data' => CounterResource::collection(Counter::all()),
            'error' => null
        ]);
    }

    public function update(CounterCreateRequest $request, int $idUser, int $idCounter)
    {
        $counter = Counter::where('id', $idCounter)->first();
        $data = $request->validated();
        $counter->name = trim($data['name']);
        $counter->user_id = $idUser;
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
}
