<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CounterResource extends JsonResource
{
   
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $service = Service::find($this->service_id) ?? null;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'operator' => $this->whenNotNull(new UserResource($this->user)),
            'service' => $this->whenNotNull(new ServiceResource($this->service)),
            'is_active' => $this->is_active,
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            "updated_at" => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')
        ];
    }
}
