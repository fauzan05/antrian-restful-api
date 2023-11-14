<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $counter = Counter::where('name', 'Loket 1')->first();
        $service = new Service();
        $service->name = 'Layanan Poli Gizi';
        $service->initial = strtoupper(chr(65));
        $service->description = 'Ini adalah layanan Poli Gizi';
        $service->counter_id = $counter->id;
        $service->save();

        $counter = Counter::where('name', 'Loket 2')->first();
        $service = new Service();
        $service->name = 'Layanan Poli Gigi';
        $service->initial = strtoupper(chr(66));
        $service->description = 'Ini adalah layanan Poli Gigi';
        $service->counter_id = $counter->id;
        $service->save();

        $counter = Counter::where('name', 'Loket 3')->first();
        $service = new Service();
        $service->name = 'Layanan Poli Jantung';
        $service->initial = strtoupper(chr(67));
        $service->description = 'Ini adalah layanan Poli Jantung';
        $service->counter_id = $counter->id;
        $service->save();

        $counter = Counter::where('name', 'Loket 1')->first();
        $service = new Service();
        $service->name = 'Layanan Poli Paru Paru';
        $service->initial = strtoupper(chr(68));
        $service->description = 'Ini adalah layanan Poli Paru Paru';
        $service->counter_id = $counter->id;
        $service->save();
    }
}
