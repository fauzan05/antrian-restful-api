<?php

namespace Tests\Feature;

use App\Http\Resources\UserCollection;
use App\Models\User;
use Database\Seeders\ManyUserSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class UserTest extends TestCase
{
    public string $tokens = "";
    public function testRegister()
    {
        $response = $this->post('/api/users/register', [
            'name' => 'Fauzan',
            'username' => 'fauzan123',
            'password' => 'rahasia',
        ]);
        $user = User::where('name', 'Fauzan')->first();
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => 'Fauzan',
                'username' => 'fauzan123',
                'role' => 'operator'
            ]
        ]);
    }

    public function testRegisterUserAlreadyRegistered()
    {
        $this->seed(UserSeeder::class);
        $response = $this->post('/api/users/register', [
            'name' => 'Fauzan',
            'username' => 'fauzan123',
            'password' => 'rahasia',
        ]);
        $response->assertStatus(400);
        $response->assertJson([
            "success" => false,
            "error_message" => 'username has been already registered'
        ]);
    }
    public function testRegisterFailed()
    {
        $this->post('/api/users/register', [])->assertStatus(400)
            ->assertJson([
                'error_message' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]);
    }

    public function testLogin()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $response = $this->post('/api/users/login', [
            'username' => 'fauzan123',
            'password' => 'rahasia'
        ]);
        $this->tokens = $response['token'];
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => 'Fauzan',
                'username' => 'fauzan123',
                'role' => 'operator'
            ]
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
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => 'Fauzan',
                    'username' => 'fauzan123',
                    'role' => 'operator'
                ]
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
            'name' => 'Rudi',
            'password' => 'gatau'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->assertStatus(200)
            ->assertJson([
                "success" => true,
                "message" => "user has been updated",
                "data" => [
                    "id" => $user->id,
                    "name" => "Rudi",
                    "username" => "fauzan123",
                    "role" => "operator"
                ]
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
                'success' => true,
                'message' => 'user has been successfully logged out'
            ]);
    }

    
}
