<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCreateRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Counter;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function create(ServiceCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $service = new Service();
        $service->initial = trim($data['initial']);
        $service->name = trim($data['name']);
        $service->role = $data['role'];
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
        ])->setStatusCode(200);
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'status' => "OK",
            'data' => ServiceResource::collection(Service::all()),
            'error' => null
        ])->setStatusCode(200);
    }

    public function update(int $idService, ServiceUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $service = Service::find($idService);
        $service->name = trim($data['name']);
        $service->initial = trim($data['initial']);
        $service->description = trim($data['description']);
        $service->fill($data);
        $service->save();
        return response()->json([
            'status' => "OK",
            'data' => new ServiceResource($service),
            'error' => null
        ])->setStatusCode(200);
    }

    public function delete(int $id): JsonResponse
    {
        /*  jika menghapus service, record antrian akan ikut terhapus
            sesuai dengan service yang ingin dihapus. Jadi hapus pada saat
            sebelum app antrian dijalankan
        */

        $service = Service::find($id);
        Counter::where('service_id', $id)->update([
            'service_id' => null
        ]);
        $service->delete();

        return response()->json([
            'status' => "OK",
            'data' => null,
            "error" => null
        ])->setStatusCode(200);
    }

    public function destroy()
    {
        $services = Service::get();
        $results = $services->pluck('id')->all();
        foreach($results as $result)
        {
            $services->find($result)->delete();
        }
        return response()->json([
            'status' => "OK",
            'data' => null,
            'error' => null
        ])->setStatusCode(200);
    }
}
