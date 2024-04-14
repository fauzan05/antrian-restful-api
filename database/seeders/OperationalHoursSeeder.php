<?php

namespace Database\Seeders;

use App\Models\OperationalHours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationalHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        for($i = 0; $i < 7; $i++)
        {
            $operationalHours = new OperationalHours();
            $operationalHours->days = $days[$i];
            $operationalHours->save();
        }
    }
}
