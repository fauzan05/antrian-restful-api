<?php

namespace Tests\Feature;

use App\Http\Resources\UserCollection;
use App\Models\User;
use Database\Seeders\CounterSeeder;
use Database\Seeders\ManyUserSeeder;
use Database\Seeders\NewServiceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegister()
    {
        $response = $this->post('/api/users/register', [
            'name' => 'Fauzan',
            'username' => 'fauzan123',
            'password' => 'rahasia',
            'password_confirmation' => 'rahasia'
        ])->assertStatus(201)
            ->assertJson([
            'status' => "OK",
            'data' => [
                'name' => 'Fauzan',
                'username' => 'fauzan123',
                'role' => 'operator'
            ],
            'error' => null
        ]);
    }

    public function testRegisterUserAlreadyRegistered()
    {
        $this->seed(UserSeeder::class);
        $response = $this->post('/api/users/register', [
            'name' => 'Fauzan',
            'username' => 'fauzan123',
            'password' => 'rahasia',
            'password_confirmation' => 'rahasia'
        ]);
        $response->assertStatus(409);
        $response->assertJson([
            "status" => "Validation Error",
            "data" => null,
            "error" => [
                "error_message" => 'username has been already registered'
            ]
        ]);
    }
    public function testRegisterFailed()
    {
        $this->post('/api/users/register', [])->assertStatus(400)
            ->assertJson([
                "status" => "Validation Error",
                "data" => null,
                "error" => [
                    'error_message' => [
                        'name' => [
                            'The name field is required.'
                        ],
                        'password' => [
                            'The password field is required.'
                        ]
                    ]
                ]
                
            ]);
    }

    public function testLogin()
    {
        $this->seed([UserSeeder::class, NewServiceSeeder::class, CounterSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $response = $this->post('/api/users/login', [
            'username' => 'fauzan123',
            'password' => 'rahasia'
    ]);
        $user->createToken('test-token')->plainTextToken;
        $response->assertStatus(200);
        $response->assertJson([
            'status' => "OK",
            'data' => [
                'id' => $user->id,
                'name' => 'Fauzan',
                'username' => 'fauzan123',
                'role' => 'operator'
            ],
            'error' => null
        ]);
    }

    public function testLoginFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            '/api/users/login',
            [
                'username' => 'fauzan123',
                'password' => 'haha123'
            ]
        )->assertStatus(401);
    }

    public function testCurrentUser()
    {
        $this->seed([UserSeeder::class]);

        $user = User::where('username', 'fauzan123')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->get('/api/users/current', headers: [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                'status' => "OK",
                'data' => [
                    'id' => $user->id,
                    'name' => 'Fauzan',
                    'username' => 'fauzan123',
                    'role' => 'operator'
                ],
                'error' => null
            ]);
    }

    public function testTokenNotValid()
    {
        $this->get('/api/users/current', [
            'Accept' => 'application/json',
            'Authorization' => "Bearer "
        ])->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function testUpdateUser()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->put('/api/users/update', 
        [
            'name' => 'Fauzan Nurhidayat',
            'old_password' => 'rahasia',
            'new_password' => 'fauzan123',
            'new_password_confirmation' => 'fauzan123'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => "OK",
                "data" => [
                    "id" => $user->id,
                    "name" => "Fauzan Nurhidayat",
                    "username" => "fauzan123",
                    "role" => "operator"
                ],
                "error" => null
            ]);
    }

    public function testGetAllUser()
    {
        $this->seed([ManyUserSeeder::class]);
        $user = User::where("username", "admin")->first();
        $token = $user->createToken("test-token")->plainTextToken;
        $this->get('/api/users', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $token
        ])->assertStatus(200);
    }

    public function testGetAllUserFailed()
    {
        // because isn't admin
        $this->seed([UserSeeder::class]);
        $user = User::where('username','fauzan123')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->get('/api/users', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $token
        ])->assertStatus(401);
    }

    public function testDeleteUserById()
    {
        $this->seed([ManyUserSeeder::class]);
        $user = User::select('id')->where('username', 'username-0')->first();
        $admin = User::where('username','admin')->first();
        $token = $admin->createToken('test-token')->plainTextToken;
        $this->delete('/api/users/' . $user->id , headers: [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $token
        ])->assertStatus(200);
    }

    public function testDeleteUserFailed()
    {
        // because non admin
        $this->seed([ManyUserSeeder::class]);
        $user = User::where('username', 'username-0')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->delete('/api/users/' . $user->id + 1, headers: [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer' . $token
        ])->assertStatus(401);
    }

    public function testLogout()
    {
        $this->seed([UserSeeder::class]);

        $user = User::where('username', 'fauzan123')->first();
        $token = $user->createToken('test-token')->plainTextToken;
      
        $this->delete('/api/users/logout', headers: [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                'status' => "OK",
                'data' => null,
                'error' => null
            ]);
    }

    
}
