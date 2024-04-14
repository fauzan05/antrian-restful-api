<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\CurrentUserUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Counter;
use Illuminate\Http\JsonResponse;
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
        $user->role = $data["role"];
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
        $user = User::where('username', $data['username'])->first();
        $success['token'] = $user->createToken('token-login', ['*'], now()->addDay())->plainTextToken;
        return response()->json([
            "status" => "OK",
            'data' => new UserResource($user),
            'token' => $success['token'],
            'error' => null
        ])->setStatusCode(200);  
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'status' => "OK",
            'data' => UserResource::collection(User::where('role', 'operator')->get()),
            'error' => null
        ])->setStatusCode(200);
    }

    public function get(): JsonResponse
    {
        return response()->json([
            "status" => "OK",
            "data" => new UserResource(auth()->user()),
            "error" => null
        ])->setStatusCode(200);
    }

    public function updateCurrentPassword(CurrentUserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();
        $user->fill($data);
        $user->password = $data['new_password'];
        $user->save();
        return response()->json([
            "status" => "OK",
            "data" => new UserResource($user),
            "error" => null
        ])->setStatusCode(200);   
    }

    public function update(int $id, UserUpdateRequest $request)
    {
        $data = $request->validated();
        $user = User::find($id);
        $user->fill($data);
        $user->password = !$data['new_password'] ? $user->password : $data['new_password'];
        $user->save();
        return response()->json([
            "status" => "OK",
            "data" => new UserResource($user),
            "error" => null
        ])->setStatusCode(200);
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
        ])->setStatusCode(200);
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
