<?php

namespace Tests\Feature;

use App\Http\Resources\QueueResource;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\CounterSeeder;
use Database\Seeders\NewServiceSeeder;
use Database\Seeders\QueueSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueueTest extends TestCase
{
    public function testCreate()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, NewServiceSeeder::class]);
        $service = Service::first();
        $response = $this->post('/api/queues', [
            'service_id' => $service->id,
        ])->assertStatus(201);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        $queue = Queue::first();
        Log::info(json_encode($queue->service['counter_id'], JSON_PRETTY_PRINT));
        
    }

    public function testGetById()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, NewServiceSeeder::class]);
        $service = Service::where('name', 'Layanan Poli Gizi')->first();
        $response = $this->post('/api/queues', [
            'service_id' => $service->id,
        ])->assertStatus(201);
        $response = $this->get('/api/queues/'. $response['data'][0]['id'])
        ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetByIdNotFound()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, NewServiceSeeder::class]);
        $service = Service::where('name', 'Layanan Poli Gizi')->first();
        $response = $this->post('/api/queues', [
            'service_id' => $service->id,
        ])->assertStatus(201);
        $response = $this->get('/api/queues/'. $response['data'][0]['id']+10)
        ->assertStatus(404);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testShowAll()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, NewServiceSeeder::class]);
        $service1 = Service::where('name', 'Layanan Poli Gizi')->first();
        $service2 = Service::where('name', 'Layanan Poli Paru Paru')->first();
        for($i = 1; $i < 10; $i++) {
            $this->post('/api/queues', [
                'service_id' => $service1->id,
            ])->assertStatus(201);
        }
        $this->post('/api/queues', [
            'service_id' => $service2->id,
        ])->assertStatus(201);
        $response = $this->get('/api/queues/')
        ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        // $user = User::first();
        // Log::info(json_encode($user->service, JSON_PRETTY_PRINT));
        // Log::info(json_encode($user->counter, JSON_PRETTY_PRINT));
    }


    public function testUpdate()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class, NewServiceSeeder::class]);
        $service1 = Service::where('name', 'Layanan Poli Gizi')->first();
        $service2 = Service::where('name', 'Layanan Poli Paru Paru')->first();
        for($i = 1; $i < 10; $i++) {
            $this->post('/api/queues', [
                'service_id' => $service1->id,
            ])->assertStatus(201);
        }
        $this->post('/api/queues', [
            'service_id' => $service2->id,
        ])->assertStatus(201);
        $queue1 = Queue::first();
        $this->put('/api/queues/' . $queue1->id, [
            'status' => 'called' 
        ])->assertStatus(200);
        $queue2 = Queue::first();
        self::assertNotEquals($queue1->status, $queue2->status);
    }

    public function testCount()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class,
        NewServiceSeeder::class, QueueSeeder::class]);
        $service = Service::where('initial', strtoupper(chr(66)))->first();
        $response = $this->get('/api/services/' . $service->id . '/queue-count')
            ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

    }

    public function testDelete()
    {
        $this->seed([UserSeeder::class, CounterSeeder::class,
        NewServiceSeeder::class, QueueSeeder::class]);
        $this->delete('/api/queues')
        ->assertStatus(200);
        self::assertNull(Queue::first());
    }

    





    

}
