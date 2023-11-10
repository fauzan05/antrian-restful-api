<?php

namespace Database\Seeders;

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
        for( $i = 1; $i <= 4; $i++ ) {
        $service = new Service();
        $service->name = "Layanan " . $i;
        $service->description = "Ini adalah layanan " . $i;
        $service->save();
        }
    }
}
