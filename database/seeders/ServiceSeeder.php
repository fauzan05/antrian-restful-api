<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $counter = Counter::first();
        for( $i = 0; $i < 4; $i++ ) {
        $service = new Service();
        $service->name = "Layanan " . $i +1;
        $service->initial = strtoupper(chr(65+$i));
        $service->description = "Ini adalah layanan " . $i+1;
        $service->counter_id = $counter->id;
        $service->save();
        }
    }
}
