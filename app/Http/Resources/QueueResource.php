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
        $host = 'http://localhost:8001/assets/audio/';
        // bagian pendaftaran
        $registrationNumber = str_split((string) $this->registration_number);
        $registrationIntval = $registrationNumber;
        array_shift($registrationIntval);
        $registrationIntval = implode($registrationIntval);
        $counterRegistration = Counter::find($this->counter_registration_id) ?? null;
        !$counterRegistration ? null : ($counterRegistration = explode(' ', $counterRegistration->name));
        // bagian poly
        $polyNumber = str_split((string) $this->poly_number);
        $polyIntval = $polyNumber;
        array_shift($polyIntval);
        $polyIntval = implode($polyIntval);
        // $counterPoly = Counter::where('id', $this->counter_poly_id)->first();
        // $counterPoly = explode(" ", $counterPoly->name);
        return [
            'id' => $this->id,
            'registration_number' => $this->registration_number,
            'poly_number' => $this->poly_number,
            'registration_service_id' => $this->registration_service_id,
            'poly_service_id' => $this->poly_service_id,
            'counter_registration_id' => $this->counter_registration_id ?? 'belum dipanggil',
            'counter_poly_id' => $this->counter_poly_id ?? 'belum dipanggil',
            'registration_status' => $this->registration_status,
            'poly_status' => $this->poly_status,
            'service_registration' => new ServiceResource($this->serviceRegistration),
            'service_poly' => new ServiceResource($this->servicePoly),
            'date' => $this->created_at->format('l, j F Y H:i:s'),
            'link-audio-registration' => isset($this->counter_registration_id)
             ? [
                $host . 'opening' . '.mp3',
                $host . 'nomor-antrian' . '.mp3',
                $host . $registrationNumber[0] . '.mp3',
                $host . intval($registrationIntval) . '.mp3',
                $host . 'silahkan-menuju-loket' . '.mp3',
                $host . $counterRegistration[1] . '.mp3'
                ] : null,
            'link-audio-poly' => isset($this->counter_registration_id)
                ? [
                    $host . 'opening' . '.mp3',
                    $host . 'nomor-antrian' . '.mp3',
                    $host . $polyNumber[0] . '.mp3',
                    $host . intval($polyIntval) . '.mp3',
                    $host . 'silahkan-menuju-loket' . '.mp3',
                    $host . $counterRegistration[1] . '.mp3',
                    // $host . $counterPoly[1],
                    // $host . $counterPoly[2],
                    // $host . $counterPoly[3]
                ]
                : null,
                "created_at" => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
                "updated_at" => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')
        ];
    }
}
