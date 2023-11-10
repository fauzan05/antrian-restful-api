<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
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
        $this->post('/api/service/create', 
        [
            'name' => 'Layanan 1',
            'description' => 'ini adalah layanan 1'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(201);
    }

    public function testCreateServiceNonAdmin()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('role', 'operator')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->post('/api/service/create', 
        [
            'name' => 'Layanan 1',
            'description' => 'ini adalah layanan 1'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(401)
            ->assertJson([
                'success' => false,
                "error_message" => "Access Denied! this action must be admin role"
            ]);
    }

    public function testGetServiceById()
    {
        $this->seed([UserSeeder::class, ServiceSeeder::class]);
        $service = Service::first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->get('/api/service/'. $service->id, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $service->id,
                'name' => 'Layanan 1',
                'description' => 'Ini adalah layanan 1'
            ]
        ]);
    }

    public function testGetServiceNonAdmin()
    {
        $this->seed([UserSeeder::class, ServiceSeeder::class]);
        $service = Service::first();
        $admin = User::where('role', 'operator')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->get('/api/service/'. $service->id, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])
        ->assertStatus(401)
        ->assertJson([
            'success' => false,
            'error_message' => "Access Denied! this action must be admin role"
        ]);
    }

    public function testGetAllService()
    {
        $this->seed([UserSeeder::class, ServiceSeeder::class]);
        $admin = User::where('role', 'operator')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->get('/api/service', 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
    }

    public function testUpdatingService()
    {
        $this->seed([UserSeeder::class, ServiceSeeder::class]);
        $serviceBefore = Service::first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->put('/api/service', 
        [
            'id' => $serviceBefore->id,
            'name' => 'Layanan CS',
            'description' => 'Ini adalah layanan CS'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);

        $serviceAfter = Service::find($serviceBefore->id);
        self::assertNotEquals($serviceBefore->name, $serviceAfter->name);
        self::assertNotEquals($serviceBefore->description, $serviceAfter->description);
    }

    public function testDeleteServiceById()
    {
        $this->seed([UserSeeder::class, ServiceSeeder::class]);
        $service = Service::first();
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/service/'. $service->id, headers:
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        self::assertNull(Service::find($service->id));
    }

    public function testDestroy()
    {
        $this->seed([UserSeeder::class, ServiceSeeder::class]);
        $admin = User::where('role', 'admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/service', headers:
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200);
        self::assertNull(Service::first());
    }

    
   




}
