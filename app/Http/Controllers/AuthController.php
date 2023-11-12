<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
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
        if(Auth::attempt($data)) {
            $auth = auth()->user();
            $success['token'] = $auth->createToken('token-login')->plainTextToken;

            return response()->json([
                "status" => "OK",
                'data' => new UserResource(auth()->user()),
                'token' => $success['token'],
                'error' => null
            ]);
        } else {
            throw new HttpResponseException(response()->json([
                "status" => "Validation Error",
                'data' => null,
                'error' => [
                    "error_message" => "username or password is wrong"
                ]
            ], 401));
        }    
    }

    public function show(): JsonResponse
    {
        $user = User::where('role', 'operator')->get();
        return response()->json([
            'status' => "OK",
            new UserCollection($user),
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
        $user = User::where('id', $id)->where('role', 'operator')->delete();
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
