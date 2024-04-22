<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueCreateRequest;
use App\Http\Requests\UpdateQueueStatusRequest;
use App\Http\Resources\CurrentQueueResource;
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
        // memilih salah satu layanan dengan role registrasi secara random
        $registrationServices = Service::select('id','initial')->where('role', 'registration')->get();
        // menampung counter dengan jumlah antrian terendah
        $lowestQueueCountService = 999999999;
        $selectedRegistrationService = null;

        // agar jika ada lebih dari 1 layanan pendaftaran, maka antriannya bisa saling bergantian dan seimbang
        foreach($registrationServices as $key => $registrationService) {
            // mencari antrian dengan layanan dipanggilnya paling sedikit
            $registrationNumber = Queue::select('id')->where('registration_service_id', $registrationService->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
            // dd($registrationNumber);
            if ($registrationNumber < $lowestQueueCountService) {
                $lowestQueueCountService = $registrationNumber;
                $selectedRegistrationService = $registrationService;
            }
        }
        $polyService = Service::select('id','initial')->where('id', $data['poly_service_id'])->first();
        $polyNumber = Queue::select('id')->where('poly_service_id', $data['poly_service_id'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        $queue = new Queue();
        $queue->registration_number = $selectedRegistrationService->initial . str_pad($lowestQueueCountService + 1, 3, '0', STR_PAD_LEFT); // A001 (contoh)
        $queue->poly_number = $polyService->initial . str_pad($polyNumber + 1, 3, '0', STR_PAD_LEFT);
        $queue->registration_service_id = $selectedRegistrationService->id;
        $queue->poly_service_id = $polyService->id;
        $queue->registration_status = 'waiting';
        $queue->poly_status = 'waiting';
        $queue->save();
        return response()->json([
                'status' => 'OK',
                'data' => new QueueResource($queue),
                'error' => 'null',
        ])->setStatusCode(201);
    }

    public function get(int $id)
    {
        $queue = Queue::where('id', $id)->first();
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue),
            'error' => null,
        ])->setStatusCode(200);
    }

    public function show()
    {
        return response()->json([
            'status' => 'OK',
            'data' => QueueResource::collection(Queue::all()),
            'error' => null,
        ])->setStatusCode(200);
    }

    public function showQueueByCounter(int $idCounter)
    {
        $counterServiceRole = DB::table('counters')
            ->join('services', 'services.id', '=', 'counters.service_id')
            ->select('services.role')
            ->where('counters.id', $idCounter)
            ->first();
        if ($counterServiceRole->role == 'registration') {
            $currentQueue = DB::table('services')
                ->join('queues', 'services.id', '=', 'queues.registration_service_id')
                ->join('counters', 'counters.service_id', '=', 'services.id')
                ->select(
                    'queues.registration_number',
                    DB::raw('services.name as service_name'),
                    'queues.registration_status',
                    DB::raw('counters.name as counters_name')
                )
                ->where('counters.id', $idCounter)
                ->orderBy('registration_number')
                ->get();
        }

        if ($counterServiceRole->role == 'poly') {
            $currentQueue = DB::table('services')
                ->join('queues', 'services.id', '=', 'queues.poly_service_id')
                ->join('counters', 'counters.service_id', '=', 'services.id')
                ->select(
                    'queues.poly_number',
                    DB::raw('services.name as service_name'),
                    'queues.poly_status',
                    DB::raw('counters.name as counters_name')
                )
                ->where('counters.id', $idCounter)
                ->whereIn('registration_status', ['called', 'skipped'])
                ->orderBy('poly_number')
                ->get();
        }

        return response()->json([
            'status' => 'OK',
            'data' => new ShowQueueResource($currentQueue),
            'error' => null,
        ])->setStatusCode(200);
    }

    public function showQueueByUser(int $idUser)
    {
        // mencari apakah user bertugas di counter pendaftaran atau poli
        $userCounterRole = DB::table('users')
            ->join('counters', 'counters.user_id', '=', 'users.id')
            ->join('services', 'services.id', '=', 'counters.service_id')
            ->select('services.role')
            ->where('users.id', $idUser)
            ->first();

        if ($userCounterRole->role == 'registration') {
            $allQueue = Service::join('queues', 'services.id', '=', 'queues.registration_service_id')
                ->join('counters', 'counters.service_id', '=', 'services.id')
                ->select(
                    'queues.id',
                    DB::raw('queues.registration_number as number'),
                    DB::raw('services.name as service_name'),
                    DB::raw('queues.registration_status as status'),
                    DB::raw('counters.name as counters_name')
                )
                ->where('counters.user_id', $idUser)
                ->whereDate('queues.created_at', Carbon::today())
                ->orderBy('queues.registration_number');
            // ->get() ?? null;
            // $queue = Queue::get();
            if (!$allQueue->get()) {
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
            return response()->json([
                'status' => 'OK',
                'data' => new ShowQueueResource($allQueue->get()),
                'data_paginate' => new ShowQueueResource($allQueue->paginate(10)),
                'error' => null,
            ]);
        }
        if ($userCounterRole->role == 'poly') {
            $allQueue = DB::table('services')
                ->join('queues', 'services.id', '=', 'queues.poly_service_id')
                ->join('counters', 'counters.service_id', '=', 'services.id')
                ->select(
                    'queues.id',
                    DB::raw('queues.poly_number as number'),
                    DB::raw('services.name as service_name'),
                    DB::raw('queues.poly_status as status'),
                    DB::raw('counters.name as counters_name')
                )
                ->where('counters.user_id', $idUser)
                ->where('queues.registration_status', 'called')
                ->whereDate('queues.created_at', Carbon::today())
                ->orderBy('queues.poly_number');

            if (!$allQueue->get()) {
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
            return response()->json([
                'status' => 'OK',
                'data' => new ShowQueueResource($allQueue->get()),
                'data_paginate' => new ShowQueueResource($allQueue->paginate(10)),
                'error' => null,
            ])->setStatusCode(200);
        }
    }

    public function update(int $idQueue, UpdateQueueStatusRequest $request)
    {
        $data = $request->validated();
        $counterServiceRole = DB::table('counters')
            ->join('services', 'services.id', '=', 'counters.service_id')
            ->select('services.role')
            ->where('counters.id', $data['counter_id'])
            ->first();

        if ($counterServiceRole->role == 'registration') {
            DB::transaction(function () use (&$idQueue, &$data) {
                Queue::where('id', $idQueue)
                    ->whereDate('created_at', Carbon::today())
                    ->update([
                        'registration_status' => $data['status'],
                        'counter_registration_id' => trim($data['counter_id']),
                    ]);
            }, 5);
        }
        if ($counterServiceRole->role == 'poly') {
            DB::transaction(function () use (&$idQueue, &$data) {
                Queue::where('id', $idQueue)
                    ->whereDate('created_at', Carbon::today())
                    ->update([
                        'poly_status' => $data['status'],
                        'counter_poly_id' => trim($data['counter_id']),
                    ]);
            }, 5);
        }
        $queue = Queue::where('id', $idQueue)
            ->whereDate('created_at', Carbon::today())
            ->get();
        return response()->json([
            'status' => 'OK',
            'data' => QueueResource::collection($queue),
            'error' => null,
        ])->setStatusCode(200);
    }

    public function currentByService(int $idService)
    {
        $service = Service::find($idService);
        if ($service->role == 'registration') {
            $queue = Queue::where('registration_service_id', $idService)
                ->whereIn('registration_status', ['called', 'skipped'])
                ->whereDate('created_at', Carbon::today())
                ->orderByDesc('updated_at')
                ->first();
            if(!$queue){
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
        }
        if ($service->role == 'poly') {
            $queue = Queue::where('poly_service_id', $idService)
                ->whereIn('poly_status', ['called', 'skipped'])
                ->whereDate('created_at', Carbon::today())
                ->orderByDesc('updated_at')
                ->first();
            if(!$queue){
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
        }
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue),
            'error' => null,
        ])->setStatusCode(200);
    }

    public function currentByCounter(int $idCounter)
    {
        $counter = Counter::find($idCounter);
        if($counter->service->role == "registration")
        {
            $queue = Queue::where('registration_service_id', $counter->service->id)
            ->whereIn('registration_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('updated_at')
            ->first();
            if(!$queue){
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
        }
        if($counter->service->role == "poly")
        {
            $queue = Queue::where('poly_service_id', $counter->service->id)
            ->whereIn('poly_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('updated_at')
            ->first();
            if(!$queue){
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
        }
        return response()->json([
            'status' => 'OK',
            'data' => new QueueResource($queue),
            'error' => null,
        ])->setStatusCode(200);
    }
    public function allCurrentQueueByCounter(int $idCounter)
    {
        $counters = Counter::where('id', $idCounter)->first();
        if ($counters->service->role == "registration") {
            $counters = Counter::where('service_id', $counters->service->id)->get();
            $queue = [];
            foreach ($counters as $counter) {
                $result = DB::table('counters')
                    ->join('services', 'counters.service_id', '=', 'services.id')
                    ->join('queues', 'services.id', '=', 'queues.registration_service_id')
                    ->select('counters.name', DB::raw('queues.registration_number as number'),)
                    ->where('counters.id', $counter->id)
                    ->where('queues.counter_registration_id', $counter->id)
                    ->whereIn('queues.registration_status', ['called', 'skipped'])
                    ->whereDate('queues.created_at', Carbon::today())
                    ->orderBy('queues.registration_number', 'desc')
                    ->first();
                if (!$result) {
                    $queue[] = [
                        'name' => $counter->name,
                        'number' => 0
                    ];
                } else {
                    $queue[] = $result;
                }
            }
            return response()->json([
                'status' => 'OK',
                'data' => new CurrentQueueResource($queue),
                'error' => null
            ]);
        }

        if ($counters->service->role == "poly") {
            $counters = Counter::join('services', 'services.id', '=', 'counters.service_id')
                ->where('services.role', $counters->service->role)
                ->get();;
            $queue = [];
            foreach ($counters as $counter) {
                $result = DB::table('counters')
                    ->join('services', 'counters.service_id', '=', 'services.id')
                    ->join('queues', 'services.id', '=', 'queues.poly_service_id')
                    ->select('counters.name', DB::raw('queues.poly_number as number'))
                    ->where('counters.id', $counter->id)
                    ->where('queues.counter_poly_id', $counter->id)
                    ->whereIn('queues.poly_status', ['called', 'skipped'])
                    ->whereDate('queues.created_at', Carbon::today())
                    ->orderBy('queues.poly_number', 'desc')
                    ->first();
                if (!$result) {
                    $queue[] = [
                        'name' => $counter->name,
                        'number' => 0
                    ];
                } else {
                    $queue[] = $result;
                }
            }
            return response()->json([
                'status' => 'OK',
                'data' => new CurrentQueueResource($queue),
                'error' => null
            ])->setStatusCode(200);
        }
    }

    public function allCurrentQueueByUser(int $idUser)
    {
        $counters = Counter::join('services', 'services.id', '=', 'counters.service_id')
            ->where('counters.user_id', $idUser)
            ->first();
        if ($counters->role == "registration") {
            $counters = Counter::where('service_id', $counters->service->id)->get();
            $queue = [];
            foreach ($counters as $key => $counter) {
                $queue[$key] = [
                    'name' => $counter->name,
                    'number' => 0
                ];
                $result = DB::table('queues')
                    ->where('queues.counter_registration_id', $counter->id)
                    ->whereIn('queues.registration_status', ['called', 'skipped'])
                    ->whereDate('queues.created_at', Carbon::today())
                    ->orderBy('queues.updated_at', 'desc')
                    ->first();
                if ($result) {
                    $queue[$key]["number"] = $result->registration_number;
                }
            }
            return response()->json([
                'status' => 'OK',
                'data' => new CurrentQueueResource($queue),
                'error' => null
            ]);
        }
        if ($counters->role == "poly") {
            $counters = Counter::join('services', 'services.id', '=', 'counters.service_id')
                ->where('services.role', $counters->service->role)
                ->get();
            $queue = [];
            foreach ($counters as $key => $counter) {
                $queue[$key] = [
                    "name" => $counter->name,
                    "number" => 0
                ];
                $result = DB::table('queues')
                    ->where('poly_service_id', $counter->service_id)
                    ->whereDate('queues.created_at', Carbon::today())
                    ->whereIn('queues.poly_status', ['called', 'skipped'])
                    ->orderBy('updated_at', 'desc')
                    ->first();
                if ($result) {
                    $queue[$key]["number"] = $result->poly_number;
                }
            }
            return response()->json([
                'status' => 'OK',
                'data' => new CurrentQueueResource($queue),
                'error' => null
            ])->setStatusCode(200);
        }
    }

    public function allCurrentQueueByEachCounters()
    {
        $counters = Counter::all();
        $queues = [];
        foreach ($counters as $key => $counter) {
            if ($counter->service->role == "registration") {
                $queues[$key] = [
                    "name" => $counter->name,
                    "number" => 0,
                    "status" => null
                ];
                $result = Queue::where('counter_registration_id', $counter->id)
                    ->whereIn('registration_status', ['called', 'skipped'])
                    ->orderby('updated_at', 'desc')
                    ->first();
                if ($result) {
                    $queues[$key]["number"] = $result->registration_number;
                    $queues[$key]["status"] = $result->registration_status;
                }
            }
            if ($counter->service->role == "poly") {
                $queues[$key] = [
                    "name" => $counter->name,
                    "number" => 0,
                    "status" => null
                ];
                $result = Queue::where('counter_poly_id', $counter->id)
                    ->whereIn('poly_status', ['called', 'skipped'])
                    ->orderby('updated_at', 'desc')
                    ->first();
                if ($result) {
                    $queues[$key]["number"] = $result->poly_number;
                    $queues[$key]["status"] = $result->poly_status;
                }
            }
        }
        return response()->json([
            'status' => 'OK',
            'data' => $queues,
            'error' => null
        ])->setStatusCode(200);
    }

    public function countAllQueue()
    {
        return response()->json([
            'status' => 'OK',
            'data' => Queue::all()->count(),
            'error' => null
        ])->setStatusCode(200);
    }

    public function destroy()
    {
        DB::delete('delete from queues');
        return response()->json([
            'status' => 'OK',
            'data' => null,
            'error' => null,
        ])->setStatusCode(200);
    }
}
