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
        $service = new Service();
        $service->name = 'Layanan Pendaftaran';
        $service->initial = strtoupper(chr(65));
        $service->role = 'registration';
        $service->description = 'Ini adalah layanan Pendaftaran';
        $service->save();

        $service = new Service();
        $service->name = 'Layanan Poli Gigi';
        $service->initial = strtoupper(chr(66));
        $service->role = 'poly';
        $service->description = 'Ini adalah layanan Poli Gigi';
        $service->save();

        $service = new Service();
        $service->name = 'Layanan Poli Jantung';
        $service->initial = strtoupper(chr(67));
        $service->role = 'poly';
        $service->description = 'Ini adalah layanan Poli Jantung';
        $service->save();

        $service = new Service();
        $service->name = 'Layanan Poli Anak';
        $service->initial = strtoupper(chr(68));
        $service->role = 'poly';
        $service->description = 'Ini adalah layanan Poli Anak';
        $service->save();

        $service = new Service();
        $service->name = 'Layanan Poli THT';
        $service->initial = strtoupper(chr(69));
        $service->role = 'poly';
        $service->description = 'Ini adalah layanan Poli THT';
        $service->save();

        
    }
}
