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
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class QueueController extends Controller
{
    public function create(QueueCreateRequest $request)
    {
        $data = $request->validated();
        $registrationService = Service::where('role', 'registration')->first();
        $registrationNumber = Queue::where('registration_service_id', $registrationService->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $polyService = Service::where('id', $data['poly_service_id'])->first();
        $polyNumber = Queue::where('poly_service_id', $data['poly_service_id'])
            ->whereDate('created_at', Carbon::today())
            ->count();
        $queue = new Queue();
        $queue->registration_number = $registrationService->initial . str_pad($registrationNumber + 1, 3, '0', STR_PAD_LEFT);
        $queue->poly_number = $polyService->initial . str_pad($polyNumber + 1, 3, '0', STR_PAD_LEFT);
        $queue->registration_service_id = $registrationService->id;
        $queue->poly_service_id = $polyService->id;
        $queue->registration_status = 'waiting';
        $queue->poly_status = 'waiting';
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
                ->orderBy('poly_number')
                ->get();
        }

        return response()->json([
            'status' => 'OK',
            'data' => new ShowQueueResource($currentQueue),
            'error' => null,
        ]);
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
            $currentQueue = DB::table('services')
                ->join('queues', 'services.id', '=', 'queues.registration_service_id')
                ->join('counters', 'counters.service_id', '=', 'services.id')
                ->select(
                    'queues.id',
                    'queues.registration_number',
                    DB::raw('services.name as service_name'),
                    'queues.registration_status',
                    DB::raw('counters.name as counters_name')
                )
                ->where('counters.user_id', $idUser)
                ->whereDate('queues.created_at', Carbon::today())
                ->orderBy('queues.registration_number')
                ->paginate(10) ?? null;
            if ($currentQueue->isEmpty()) {
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
            return response()->json([
                'status' => 'OK',
                'data' => new ShowQueueResource($currentQueue),
                'error' => null,
            ]);
        }
        if ($userCounterRole->role == 'poly') {
            $currentQueue = DB::table('services')
                ->join('queues', 'services.id', '=', 'queues.poly_service_id')
                ->join('counters', 'counters.service_id', '=', 'services.id')
                ->select(
                    'queues.id',
                    'queues.poly_number',
                    DB::raw('services.name as service_name'),
                    'queues.poly_status',
                    DB::raw('counters.name as counters_name')
                )
                ->where('counters.user_id', $idUser)
                ->where('queues.registration_status', 'called')
                ->whereDate('queues.created_at', Carbon::today())
                ->orderBy('queues.poly_number')
                ->paginate(10) ?? null;
            if ($currentQueue->isEmpty()) {
                return response()->json([
                    'status' => 'OK',
                    'data' => null,
                    'error' => null,
                ]);
            }
            return response()->json([
                'status' => 'OK',
                'data' => new ShowQueueResource($currentQueue),
                'error' => null,
            ]);
        }
    }

    public function update(int $idQueue, Request $request)
    {
        $data = Validator::make($request::all(), [
            "counter_id" => ['required', 'integer']
        ])->validate();
        $counterServiceRole = DB::table('counters')
            ->join('services', 'services.id', '=', 'counters.service_id')
            ->select('services.role')
            ->where('counters.id', $data['counter_id'])
            ->first();
            
        if ($counterServiceRole->role == 'registration') {
            $validateRegisterStatus = Validator::make($request::all(), [
                "registration_status" => ['required', 'string'],
            ])->validate();
            DB::transaction(function () use (&$idQueue, &$data, &$validateRegisterStatus) {
                Queue::where('id', $idQueue)
                    ->whereDate('created_at', Carbon::today())
                    ->update([
                        'registration_status' => $validateRegisterStatus['registration_status'],
                        'counter_registration_id' => trim($data['counter_id']),
                    ]);
            }, 5);
        }
        if ($counterServiceRole->role == 'poly') {
            $validatePolyStatus = Validator::make($request::all(), [
                "poly_status" => ['required', 'string'],
            ])->validate();
            DB::transaction(function () use (&$idQueue, &$data, &$validatePolyStatus) {
                Queue::where('id', $idQueue)
                    ->whereDate('created_at', Carbon::today())
                    ->update([
                        'poly_status' => $validatePolyStatus['poly_status'],
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
        ]);
    }

    public function currentByService(int $idService)
    {
        $service = Service::where('id', $idService)->first();
        if($service->role == 'registration')
        {
            $queue = Queue::where('registration_service_id', $idService)
            ->whereIn('registration_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('registration_number')
            ->first();
        }
        if($service->role == 'poly')
        {
            $queue = Queue::where('poly_service_id', $idService)
            ->whereIn('poly_status', ['called', 'skipped'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('poly_number')
            ->first();
        }
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
