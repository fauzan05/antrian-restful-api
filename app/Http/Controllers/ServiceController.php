<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCreateRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    
    public function create(ServiceCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $service = new Service($data);
        $service->save();

        return response()->json([
            "success" => true,
            "data" => new ServiceResource($service)
        ])->setStatusCode(201);
    }

    public function get(int $id): JsonResponse
    {
        $service = Service::find($id);
        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service)
        ]);
    }

    public function show(): JsonResponse
    {
        $service = Service::all();
        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($service)
        ]);
    }

    public function update(ServiceUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $service = Service::find($id);
        $service->fill($data);
        $service->save();
        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service)
        ])->setStatusCode(200);
    }

    public function delete(int $id): JsonResponse
    {
        $service = Service::find($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'the user has been successfully deleted'
        ]);
    }

    public function destroy()
    {
        Service::truncate();

        return response()->json([
            'success' => true,
            'message' => 'all user has been deleted'
        ]);
    }
}
