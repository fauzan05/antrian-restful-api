<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\CounterSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\NewServiceSeeder;
use Tests\TestCase;

class CounterTest extends TestCase
{
    public function testCreateCounter()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $service = Service::where('initial', 'A')->first();
        $this->post('/api/counters', 
        [
            'name' => 'Loket 1',
            'user_id' => $user->id,
            'service_id' => null
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(201); 
    }

    public function testCreateCounterFailed()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $admin = User::where('role', 'operator')->first();
        $service = Service::where('initial', 'A')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->post('/api/counters', 
        [
            'name' => 'Loket 1',
            'user_id' => $user->id,
            'service_id' => $service->id
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(401)
            ->assertJson([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    "error_message" => "Access Denied! this action must be admin role"
                ]
            ]);
        $user = User::where('username', 'susi123')->first();
        $service = Service::where('initial', 'B')->first();
        $this->post('/api/counters', 
        [
            'name' => 'Loket 1',
            'user_id' => $user->id,
            'service_id' => $service->id
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(401)
            ->assertJson([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    "error_message" => "Access Denied! this action must be admin role"
                ]
            ]);
    }
    
    public function testGetCounterById()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $user = User::where("username", "fauzan123")->first();
        $counter = Counter::where('name', 'Loket 1')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->get('/api/counters/' . $counter->id, 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                'status' => 'OK',
                'data' => [
                    'id' => $counter->id,
                    'name'=> 'Loket 1',
                    'operator' => [
                        'name' => 'Fauzan'
                    ]
                    ],
                'error' => null
            ]);
    }

    public function testGetFailed()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $user = User::where("username", "fauzan123")->first();
        $counter = Counter::where('name', 'Loket 1')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->get('/api/counters/' . $counter->id + 10, 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(404)
            ->assertJson([
                "status" => "Not Found",
                "data" => null,
                "error" => [
                    "error_message" => 'counter is not found'
                ]
            ]);    
    }

    public function testGetAllCounterHasEmpty()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->get('/api/counters', 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => "OK",
                "data" => [],
                "error" => null
            ]);     
    }

    public function testGetAllCounter()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $counter = Counter::all();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->get('/api/counters', 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => "OK",
                "data" => [
                    [
                        'id' => $counter[0]->id,
                        'name' => 'Loket 1',
                        'operator' => [
                            'name' => 'Fauzan',
                        ]
                    ],
                    [
                        'id' => $counter[1]->id,
                        'name' => 'Loket 2',
                        'operator' => [
                            'name' => 'Susi',
                        ]
                    ],
                    [
                        'id' => $counter[2]->id,
                        'name' => 'Loket 3',
                        'operator' => [
                            'name' => 'Rudi',
                        ]
                    ],
                   
                ],
                "error" => null
            ]);
    }

    public function testUpdateCounter()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $counter1 = Counter::where('name', 'Loket 3')->first();
        $service = Service::where('initial', 'A')->first();
        $user = User::where('username', 'rudi123')->first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->put('/api/counters/' . $counter1->id, 
        [
            'name' => 'Loket CS',
            'user_id' => $user->id,
            'service_id' => $service->id
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        $counter2 = Counter::where('name', 'Loket CS')->first();
        self::assertNotEquals($counter1->name, $counter2->name);
    }

    public function testDeleteById()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $counter = Counter::where('name','Loket 1')->first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/counters/'. $counter->id, headers:
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        self::assertNull(Counter::where('id', $counter->id)->first());
    }

    public function testDestroy()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/counters', headers: 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        self::assertNull(Counter::where('name', 'Loket 1')->first());
        self::assertNull(Counter::where('name', 'Loket 2')->first());
        self::assertNull(Counter::where('name', 'Loket 3')->first());

    }
}
