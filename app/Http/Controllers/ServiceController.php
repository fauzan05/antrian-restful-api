<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCreateRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{ 
    public function create(ServiceCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $service = new Service();
        $service->initial = trim($data['initial']);
        $service->name = trim($data['name']);
        $service->description = trim($data['description']);
        $service->save();

        return response()->json([
            "status" => "OK",
            "data" => new ServiceResource($service),
            "error" => null
        ])->setStatusCode(201);
    }

    public function get(int $id): JsonResponse
    {
        $service = Service::find($id);
        return response()->json([
            'status' => "OK",
            'data' => new ServiceResource($service),
            'error' => null
        ]);
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'status' => "OK",
            'data' => ServiceResource::collection(Service::all()),
            'error' => null
        ]);
    }

    public function update(int $idService, ServiceUpdateRequest $request): JsonResponse
    {
        $service = Service::find($idService);
        $data = $request->validated();
        $service->initial = trim($data['initial']);
        $service->name = trim($data['name']);
        $service->description = trim($data['description']);
        $service->fill($data);
        $service->save();
        $service = Service::find($idService);
        return response()->json([
            'status' => "OK",
            'data' => new ServiceResource($service),
            'error' => null
        ])->setStatusCode(200);
    }

    public function delete(int $id): JsonResponse
    {
        $service = Service::find($id);
        $service->delete();

        return response()->json([
            'status' => "OK",
            'data' => [
                'message' => 'the user has been successfully deleted'
            ],
            "error" => null
        ]);
    }

    public function destroy()
    {
        DB::delete("delete from services");
        return response()->json([
            'status' => "OK",
            'data' => null,
            'error' => null
        ]);
    }
}
