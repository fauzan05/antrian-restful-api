<?php

namespace Database\Seeders;

use App\Models\Queue;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class QueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service1 = Service::where('initial', strtoupper(chr(65)))->first(); // pendaftaran
        $service2 = Service::where('initial', strtoupper(chr(66)))->first(); // poly gigi
        $service3 = Service::where('initial', strtoupper(chr(67)))->first(); // poli jantung
        $service4 = Service::where('initial', strtoupper(chr(68)))->first(); // poli anak
        $service5 = Service::where('initial', strtoupper(chr(69)))->first(); // poli THT

        // poli gigi
        for ($i = 1; $i <= 5; $i++) :
            $registrationNumber = $this->checkRegistrationNumber($service1->id);
            $polyNumber = $this->checkPolyNumber($service2->id);
            $queue = new Queue();
            $queue->registration_number = $service1->initial . str_pad($registrationNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->poly_number = $service2->initial . str_pad($polyNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->registration_service_id = $service1->id;
            $queue->poly_service_id = $service2->id;
            $queue->save();
        endfor;

        //poli jantung
        for ($i = 1; $i <= 5; $i++) :
            $registrationNumber = $this->checkRegistrationNumber($service1->id);
            $polyNumber = $this->checkPolyNumber($service3->id);
            $queue = new Queue();
            $queue->registration_number = $service1->initial . str_pad($registrationNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->poly_number = $service3->initial . str_pad($polyNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->registration_service_id = $service1->id;
            $queue->poly_service_id = $service3->id;
            $queue->save();
        endfor;

        // poli anak
        for ($i = 1; $i <= 5; $i++) :
            $registrationNumber = $this->checkRegistrationNumber($service1->id);
            $polyNumber = $this->checkPolyNumber($service4->id);
            $queue = new Queue();
            $queue->registration_number = $service1->initial . str_pad($registrationNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->poly_number = $service4->initial . str_pad($polyNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->registration_service_id = $service1->id;
            $queue->poly_service_id = $service4->id;
            $queue->save();
        endfor;

        // poli THT
        for ($i = 1; $i <= 5; $i++) :
            $registrationNumber = $this->checkRegistrationNumber($service1->id);
            $polyNumber = $this->checkPolyNumber($service5->id);
            $queue = new Queue();
            $queue->registration_number = $service1->initial . str_pad($registrationNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->poly_number = $service5->initial . str_pad($polyNumber + 1, 3, '0', STR_PAD_LEFT);
            $queue->registration_service_id = $service1->id;
            $queue->poly_service_id = $service5->id;
            $queue->save();
        endfor;
    }

    public function checkRegistrationNumber(int $idService): int
    {
        return Queue::where('registration_service_id', $idService)
            ->whereDate('created_at', Carbon::today())->count();
    }
    public function checkPolyNumber(int $idService): int
    {
        return Queue::where('poly_service_id', $idService)
            ->whereDate('created_at' , Carbon::today())->count();
    }
}
