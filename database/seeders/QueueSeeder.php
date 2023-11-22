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
        $service1 = Service::where('initial', strtoupper(chr(65)))->first();
        $service2 = Service::where('initial', strtoupper(chr(66)))->first();
        $service3 = Service::where('initial', strtoupper(chr(67)))->first();
        $service4 = Service::where('initial', strtoupper(chr(68)))->first();
        
        for( $i = 1; $i <= 5; $i++ ):
        $number = $this->checkNumber($service1->id);
        $queue = new Queue();
        $queue->number = $service1->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service1->id;
        $queue->status = 'waiting';
        $queue->save();

        $number = $this->checkNumber($service1->id);
        $queue = new Queue();
        $queue->number = $service1->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service1->id;
        $queue->status = 'called';
        $queue->save();
        endfor;
        // skipped
        $number = $this->checkNumber($service1->id);
        $queue = new Queue();
        $queue->number = $service1->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service1->id;
        $queue->status = 'skipped';
        $queue->save();

        for( $i = 1; $i <= 5; $i++ ):
        $number = $this->checkNumber($service2->id);
        $queue = new Queue();
        $queue->number = $service2->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service2->id;
        $queue->status = 'waiting';
        $queue->save();

        $number = $this->checkNumber($service2->id);
        $queue = new Queue();
        $queue->number = $service2->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service2->id;
        $queue->status = 'called';
        $queue->save();
        endfor;
        // skipped
        $number = $this->checkNumber($service2->id);
        $queue = new Queue();
        $queue->number = $service2->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service2->id;
        $queue->status = 'skipped';
        $queue->save();

        for( $i = 1; $i <= 5; $i++ ):
        $number = $this->checkNumber($service3->id);
        $queue = new Queue();
        $queue->number = $service3->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service3->id;
        $queue->status = 'waiting';
        $queue->save();

        $number = $this->checkNumber($service3->id);
        $queue = new Queue();
        $queue->number = $service3->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service3->id;
        $queue->status = 'called';
        $queue->save();
        endfor;
        // skipped
        $number = $this->checkNumber($service3->id);
        $queue = new Queue();
        $queue->number = $service3->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service3->id;
        $queue->status = 'skipped';
        $queue->save();

        for( $i = 1; $i <= 5; $i++ ):
        $number = $this->checkNumber($service4->id);
        $queue = new Queue();
        $queue->number = $service4->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service4->id;
        $queue->status = 'waiting';
        $queue->save();

        $number = $this->checkNumber($service4->id);
        $queue = new Queue();
        $queue->number = $service4->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service4->id;
        $queue->status = 'called';
        $queue->save();
        endfor;
        // skipped
        $number = $this->checkNumber($service4->id);
        $queue = new Queue();
        $queue->number = $service4->initial . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        $queue->service_id = $service4->id;
        $queue->status = 'skipped';
        $queue->save();
    }

    public function checkNumber(int $idService): int
    {
        return Queue::where('service_id', $idService)
        ->whereDate('created_at', Carbon::today())->count();
    }
}
