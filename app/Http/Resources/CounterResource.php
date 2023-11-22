<?php

namespace App\Http\Resources;

use App\Models\Counter;
use App\Models\Service;
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
            'operator' => $this->whenNotNull(new UserNameResource($this->user)),
            'service' => $this->whenNotNull(new ServiceResource($this->service))
        ];
    }
}
