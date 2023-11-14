<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "number" => $this->number,
            "status" => $this->status,
            "service" => new ServiceResource($this->service),
            "counter" => CounterResource::collection($this->counter),
            "date" => $this->created_at->format("l, j F Y H:i:s"),
        ];
    }
}
