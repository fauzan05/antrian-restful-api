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
        $host = "http://127.0.0.1:8000/api/files/";
        $number = str_split((string)$this->number);
        $counter = explode(" ", $this->counter[0]->name);
        return [
            "id" => $this->id,
            "number" => $this->number,
            "status" => $this->status,
            "service" => new ServiceResource($this->service),
            "counter" => new CounterResource($this->counter->all()[0]),
            "date" => $this->created_at->format("l, j F Y H:i:s"),
            "link-audio" => [
                "link-1" => $host . "opening",
                "link-2" => $host . "nomor-antrian",
                "link-3" => $host . $number[0], // A, B, C, etc
                "link-4" => $host . $number[1], // digit 1
                "link-5" => $host . $number[2], // digit 2
                "link-6" => $host . $number[3], // digit 3
                "link-7" => $host . "silahkan-menuju-loket",
                "link-8" => $host . $counter[1],
            ]
        ];
    }
}
