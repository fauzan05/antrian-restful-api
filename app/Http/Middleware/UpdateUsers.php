<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!isset($request->idUser) && empty($request->idUser))
    {
            $user = User::find(auth()->user()->id);
            if(!$user || !Hash::check($request->old_password, $user->password))
            {
                throw new HttpResponseException(response()->json([
                    "status" => "Validation Error",
                    'data' => null,
                    'error' => [
                        "error_message" => "password lama salah"
                    ]
                ], 401));
            }
        }
        if(isset($request->idUser) && !empty($request->idUser)){
            $user = User::find($request->idUser);
            if(!$user){
                throw new HttpResponseException(response()->json([
                    "status" => "Not Found",
                    'data' => null,
                    'error' => [
                        "error_message" => "user tidak ditemukan"
                    ]
                ], 401)); 
            }
            if(!$user || !Hash::check($request->old_password, $user->password))
            {
                throw new HttpResponseException(response()->json([
                    "status" => "Validation Error",
                    'data' => null,
                    'error' => [
                        "error_message" => "password lama salah"
                    ]
                ], 401)); 
            }
        }

        if(!empty($request->username)){
            $currentUser = User::find($request->idUser);
            $anotherUser = User::where('username', $request->username)->first();
            
            if($anotherUser && $anotherUser->username != $currentUser->username){
                throw new HttpResponseException(response()->json([
                    "status" => "Conflict",
                    'data' => null,
                    'error' => [
                        "error_message" => "username sudah digunakan operator lain"
                    ]
                ], 409)); 
            }

        }
        return $next($request);
        
    }
}
