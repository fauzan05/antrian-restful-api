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
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueueTest extends TestCase
{
    public function testCreate()
    {
        $this->seed([UserSeeder::class,  NewServiceSeeder::class, CounterSeeder::class]);
        $service = Service::first();
        $response = $this->post('/api/queues', [
            'service_id' => $service->id,
        ])->assertStatus(201);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        $queue = Queue::first();
        Log::info(json_encode($queue->service['counter_id'], JSON_PRETTY_PRINT));
    }

    public function testGetQueueById()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class,
        QueueSeeder::class]);
        $service = Service::where('initial', 'A')->first();
        $queue = Queue::where('service_id', $service->id)->first();
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

    public function testShowAll()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class, 
        QueueSeeder::class]);
        $response = $this->get('/api/queues/')
        ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdate()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class,
        NewQueueSeeder::class]);
        $user = User::where('role', 'operator')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $queue1 = Queue::first();
        $counter = Counter::where('name', 'Loket 1')->first();
        $this->put('/api/queues/' . $queue1->id, 
        [
            'status' => 'called',
            'counter_id' => $counter->id
        ], 
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token 
        ])->assertStatus(200);
        $queue2 = Queue::first();
        self::assertNotEquals($queue1->status, $queue2->status);
    }

    public function testCount()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class,
        CounterSeeder::class, QueueSeeder::class]);
        $service = Service::where('initial', strtoupper(chr(66)))->first();
        $response = $this->get('/api/queues/services/' . $service->id . '/current')
            ->assertStatus(200);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

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
