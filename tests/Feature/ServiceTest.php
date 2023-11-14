<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\CounterSeeder;
use Database\Seeders\ServiceSeed;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    public function testCreateService()
    {
        $this->seed([UserSeeder::class]);
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->post('/api/services', 
        [
            'name' => 'Layanan 1',
            'initial' => 'A',
            'description' => 'ini adalah layanan 1'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(201);
    }
    public function testCreateServiceDuplicateInitial()
    {
        $this->seed([UserSeeder::class]);
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->post('/api/services', 
        [
            'name' => 'Layanan 1',
            'initial' => 'A',
            'description' => 'ini adalah layanan 1'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(201);
        $this->post('/api/services', 
        [
            'name' => 'Layanan 2',
            'initial' => 'A',
            'description' => 'ini adalah layanan 2'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(404)
            ->assertJson([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    "error_message" => 'initial service has been used'
                ]
            ]);
    }

    public function testCreateServiceNonAdmin()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('role', 'operator')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->post('/api/services', 
        [
            'name' => 'Layanan 1',
            'initial' => 'A',
            'description' => 'ini adalah layanan 1'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(401)
            ->assertJson([
                'status' => "Validation Error",
                'data' => null,
                'error' => [
                    "error_message" => "Access Denied! this action must be admin role"
                ]
            ]);
    }

    public function testGetServiceById()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $service = Service::first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->get('/api/services/'. $service->id, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])
        ->assertStatus(200)
        ->assertJson([
            'status' => "OK",
            'data' => [
                'id' => $service->id,
                'name' => 'Layanan 1',
                'initial' => 'A',
                'description' => 'Ini adalah layanan 1'
            ],
            'error' => null
        ]);
    }

    public function testGetServiceNonAdmin()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $service = Service::first();
        $admin = User::where('role', 'operator')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->get('/api/services/'. $service->id, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])
        ->assertStatus(401)
        ->assertJson([
            'status' => "Validation Error",
            'data' => null,
            'error' => [
                'error_message' => "Access Denied! this action must be admin role"
            ]
        ]);
    }

    public function testGetAllService()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $admin = User::where('role', 'operator')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->get('/api/services', 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
    }

    public function testUpdatingService()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $serviceBefore = Service::first();
        $counter = Counter::where('name', 'Loket 2')->first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->put('/api/services/'. $serviceBefore->id, 
        [
            'name' => 'Layanan CS',
            'initial' => 'Z',
            'description' => 'Ini adalah layanan CS',
            'counter_id' => $counter->id
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);

        $serviceAfter = Service::find($serviceBefore->id);
        self::assertNotEquals($serviceBefore->name, $serviceAfter->name);
        self::assertNotEquals($serviceBefore->description, $serviceAfter->description);
        self::assertNotEquals($serviceBefore->initial, $serviceAfter->initial);
        self::assertNotEquals($serviceBefore->counter_id, $serviceAfter->counter_id);
    }
    
    public function testUpdatingServiceCounterIdOnNull()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $serviceBefore = Service::first();
        $counter = 1;
        // $counter = Counter::where('name', 'Loket 2')->first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->put('/api/services/'. $serviceBefore->id , 
        [
            'name' => 'Layanan CS',
            'initial' => 'Z',
            'description' => 'Ini adalah layanan CS',
            // 'counter_id' => $counter->id
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);

        $serviceAfter = Service::find($serviceBefore->id);
        self::assertNotEquals($serviceBefore->name, $serviceAfter->name);
        self::assertNotEquals($serviceBefore->description, $serviceAfter->description);
        self::assertNotEquals($serviceBefore->initial, $serviceAfter->initial);
        self::assertEquals($serviceBefore->counter_id, $serviceAfter->counter_id);
    }

    public function testDeleteServiceById()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $service = Service::first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/services/'. $service->id, headers:
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        self::assertNull(Service::find($service->id));
    }

    public function testDestroy()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, ServiceSeeder::class]);
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/services', headers:
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        self::assertNull(Service::first());
    }

    public function testNumberPrefixs()
    {
        $id = 20000;
        $invID = str_pad($id, 4, '0', STR_PAD_LEFT);
        // var_dump($invID);
        self::assertTrue(true);
    }

}
