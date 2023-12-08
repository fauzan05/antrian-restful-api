<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'fauzan123')->first();
        $service = Service::where('initial', 'A')->first();
        Counter::create([
            'name' => 'Loket 1',
            'user_id' => $user->id,
            'service_id' => $service->id
        ]);
        $user = User::where('username', 'susi123')->first();
        $service = Service::where('initial', 'A')->first();
        Counter::create([
            'name' => 'Loket 2',
            'user_id' => $user->id,
            'service_id' => $service->id
        ]);
        $user = User::where('username', 'rudi123')->first();
        $service = Service::where('initial', 'B')->first();
        Counter::create([
            'name' => 'Loket ' . $service->name,
            'user_id' => $user->id,
            'service_id' => $service->id
        ]);
        $user = User::where('username', 'heri123')->first();
        $service = Service::where('initial', 'C')->first();
        Counter::create([
            'name' => 'Loket ' . $service->name,
            'user_id' => $user->id,
            'service_id' => $service->id
        ]);
        $user = User::where('username', 'bela123')->first();
        $service = Service::where('initial', 'D')->first();
        Counter::create([
            'name' => 'Loket ' . $service->name,
            'user_id' => $user->id,
            'service_id' => $service->id
        ]);
        $user = User::where('username', 'indri123')->first();
        $service = Service::where('initial', 'E')->first();
        Counter::create([
            'name' => 'Loket ' . $service->name,
            'user_id' => $user->id,
            'service_id' => $service->id
        ]);

    }
}
