<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Counter;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->name = trim($data['name']);
        $user->username = trim($data['username']);
        $user->password = Hash::make($data['password']);
        $user->role = 'operator';
        $user->save();

        return response()->json([
            'status' => "OK",
            'data' => new UserResource($user),
            'error' => null
        ])->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('username', trim($data['username']))->first();
        $success['token'] = $user->createToken('token-login')->plainTextToken;
        return response()->json([
            "status" => "OK",
            'data' => new UserResource($user),
            'token' => $success['token'],
            'error' => null
        ]);  
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'status' => "OK",
            'data' => UserResource::collection(User::where('role', 'operator')->get()),
            'error' => null
        ]);
    }

    public function get(): JsonResponse
    {
        return response()->json([
            "status" => "OK",
            "data" => new UserResource(auth()->user()),
            "error" => null
        ]);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();
        $user->fill($data);
        $user->save();
        return response()->json([
            "status" => "OK",
            "data" => new UserResource($user),
            "error" => null
        ]);   
    }

    public function delete(int $id): JsonResponse
    {
        // menghapus user berarti menghapus counter juga
        Counter::where('user_id', $id)->delete();
        User::where('id', $id)->where('role', 'operator')->delete();
        return response()->json([
            'status' => "OK",
            'data' => null,
            'error' => null
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();

        return response()->json([
            "status" => "OK",
            'data' => null,
            'error' => null
        ])->setStatusCode(200);
    }

}
