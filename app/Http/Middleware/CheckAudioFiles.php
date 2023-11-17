<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CheckAudioFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $files = Storage::files('public/audio');
        $url_storage = Storage::path('public/audio/' . $request->nameFile . '.mp3');
        $isExist = file_exists($url_storage);
        if(!$isExist && empty($files)) {
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                "data" => null,
                "isExist" => $isExist,
                "error" => [
                    "error_message" => "Files has empty"
                ]
            ], 404));
        }
        return $next($request);
    }
}
