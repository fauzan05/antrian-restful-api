<?php

namespace App\Http\Resources;

use App\Models\Counter;
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
        $intval = $number;
        array_shift($intval);
        $intval = implode($intval);
        $counter = Counter::where('service_id', $this->service->id)->first();
        $counter = explode(" ", $counter->name);
        return [
            "id" => $this->id,
            "number" => $this->number,
            "status" => $this->status,
            "service" => new ServiceResource($this->service),
            "date" => $this->created_at->format("l, j F Y H:i:s"),
            "link-audio" => [
                $host . "opening",
                $host . "nomor-antrian",
                $host . $number[0],
                $host . intval($intval),
                $host . "silahkan-menuju-loket",
                $host . $counter[1],
            ]
        ];
    }
}
