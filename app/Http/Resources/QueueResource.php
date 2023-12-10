<?php

namespace App\Http\Resources;

use App\Models\Counter;
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
        // bagian pendaftaran
        $registrationNumber = str_split((string)$this->registration_number);
        $registrationIntval = $registrationNumber;
        array_shift($registrationIntval);
        $registrationIntval = implode($registrationIntval);
        $counterRegistration = Counter::where('service_id', $this->serviceRegistration->id)->first();
        $counterRegistration = explode(" ", $counterRegistration->name);
        // bagian poly
        $polyNumber = str_split((string)$this->poly_number);
        $polyIntval = $polyNumber;
        array_shift($polyIntval);
        $polyIntval = implode($polyIntval);
        $counterPoly = Counter::where('service_id', $this->servicePoly->id)->first();
        $counterPoly = explode(" ", $counterPoly->name);
        
        return [
            "id" => $this->id,
            "registration_number" => $this->registration_number,
            "poly_number" => $this->poly_number,
            "registration_service_id" => $this->registration_service_id,
            "poly_service_id" => $this->poly_service_id,
            "counter_registration_id" => $this->counter_registration_id ?? "belum dipanggil",
            "counter_poly_id" => $this->counter_poly_id ?? "belum dipanggil",
            "registration_status" => $this->registration_status,
            "poly_status" => $this->poly_status,
            "service_registration" => new ServiceResource($this->serviceRegistration),
            "service_poly" => new ServiceResource($this->servicePoly),
            "date" => $this->created_at->format("l, j F Y H:i:s"),
            "link-audio-registration" => [
                $host . "opening",
                $host . "nomor-antrian",
                $host . $registrationNumber[0],
                $host . intval($registrationIntval),
                $host . "silahkan-menuju-loket",
                $host . $counterRegistration[1],
            ],
            "link-audio-poly" => [
                $host . "opening",
                $host . "nomor-antrian",
                $host . $polyNumber[0],
                $host . intval($polyIntval),
                $host . "silahkan-menuju-loket",
                $host . $counterPoly[1],
                $host . $counterPoly[2],
                $host . $counterPoly[3]
            ]
        ];
    }
}
