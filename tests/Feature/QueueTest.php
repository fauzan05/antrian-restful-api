<?php

namespace Tests\Feature;

use App\Http\Resources\QueueResource;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\CounterSeeder;
use Database\Seeders\NewQueueSeeder;
use Database\Seeders\NewServiceSeeder;
use Database\Seeders\QueueSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueueTest extends TestCase
{
    public function testCreateQueue()
    {
        $this->seed([UserSeeder::class,  NewServiceSeeder::class, CounterSeeder::class]);
        $service = Service::where('initial', 'B')->first();
        $response = $this->post('/api/queues', [
            'poly_service_id' => $service->id,
        ])->assertStatus(201);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        $queue = Queue::first();
        Log::info(json_encode($queue->servicePoly, JSON_PRETTY_PRINT));
    }

    public function testGetQueueById()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class,
        QueueSeeder::class]);
        $service = Service::where('role', 'poly')->first();
        $queue = Queue::where('poly_service_id', $service->id)->first();
        $response = $this->get('/api/queues/' . $queue->id)
        ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetByIdNotFound()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class,
        QueueSeeder::class]);
        $queue = Queue::first();
        $response = $this->get('/api/queues/'. $queue->id + 10000)
        ->assertStatus(404);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testShowAllQueues()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class, 
        QueueSeeder::class]);
        $response = $this->get('/api/queues/')
        ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateQueue()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class,
        NewQueueSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $queue1 = Queue::first();
        $counter = Counter::where('name', 'Loket 1')->first();
        $this->put('/api/queues/' . $queue1->id, 
        [
            'registration_status' => 'called',
            'counter_id' => $counter->id
        ], 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token 
        ])->assertStatus(200);
        $queue2 = Queue::first();
        self::assertNotEquals($queue1->registration_status, $queue2->registration_status);
    }

    public function testCount()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class,
        CounterSeeder::class, QueueSeeder::class]);
        $service = Service::where('initial', strtoupper(chr(65)))->first();
        $response = $this->get('/api/queues/services/' . $service->id . '/current')
            ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testShowAllQueueByUserId()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class,
        CounterSeeder::class, QueueSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $this->get('/api/queues/users/' . $user->id)
            ->assertStatus(200);
    }

    public function testShowQueueByCounter()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class,
        CounterSeeder::class, QueueSeeder::class]);
        $counter = Counter::where('name', 'Loket 1')->first();
        $response = $this->get('/api/queues/counters/' . $counter->id)
        ->assertStatus(200);
    }

    public function testCurrentQueueByService()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class,
        CounterSeeder::class, QueueSeeder::class]); 
        $service = Service::where('initial', 'B')->first();
        var_dump($service->role);
    }

    public function testDelete()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class,
        CounterSeeder::class, NewQueueSeeder::class]);
        $this->delete('/api/queues')
        ->assertStatus(200);
        self::assertNull(Queue::first());
    }

}
