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
        if(User::where('username', $data['username'])->first() !== null) {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "error_message" => 'username has been already registered'
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->role = 'operator';
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        if(Auth::attempt($data)) {
            $auth = auth()->user();
            $success['token'] = $auth->createToken('token-login')->plainTextToken;

            return response()->json([
                "success" => true,
                'data' => new UserResource(auth()->user()),
                'token' => $success['token']
            ]);
        } else {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "error_message" => "username or password is wrong"
            ], 401));
        }    
    }

    public function get()
    {
        $user = auth()->user();
        if($user->role != 'admin'){
            throw new HttpResponseException(response()->json([
                "success" => false,
                "error_message" => "getting data user must be admin role"
            ], 401));
        }
        $user = User::where('role', 'operator')->get();
        return new UserCollection($user);
    }

    public function currentUser(): JsonResponse
    {
        return response()->json([
            "success" => true,
            "data" => new UserResource(auth()->user())
        ]);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->user();
        if(isset($data['name'])){
            $user->name = $data['name'];
        }

        if(isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        return response()->json([
            "success" => true,
            "message" => "user has been updated",
            "data" => new UserResource($user)
        ]);   
    }

    public function delete(int $id): JsonResponse
    {
        $user = auth()->user();
        if($user->role != 'admin'){
            throw new HttpResponseException(response()->json([
                "success" => false,
                "error_message" => "deleting data user must be admin role"
            ], 401));
        }
        $user = User::where('id', $id)->where('role', 'operator')->delete();
        return response()->json([
            'success'=> true,
            'message' => 'user has been deleted by id'
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();

        return response()->json([
            "success" => true,
            "message" => "user has been successfully logged out"
        ])->setStatusCode(200);
    }

}
