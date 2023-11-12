<?php

namespace Database\Seeders;

use App\Models\Counter;
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
        Counter::create([
            'name' => 'Loket 1',
            'user_id' => $user->id
        ]);
        $user = User::where('username', 'susi123')->first();
        Counter::create([
            'name' => 'Loket 2',
            'user_id' => $user->id
        ]);
        $user = User::where('username', 'rudi123')->first();
        Counter::create([
            'name' => 'Loket 3',
            'user_id' => $user->id
        ]);
    }
}
