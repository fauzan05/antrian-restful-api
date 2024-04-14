<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $audios = Storage::files('public/audio');
        $allowedExtensions = ['mp3', 'aac'];
        $audios = array_filter($audios, function ($file) use ($allowedExtensions) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            return in_array($extension, $allowedExtensions);
        });
        if(!$audios && empty($audios)) {
            throw new HttpResponseException(response()->json([
                "status" => "Not Found",
                "data" => null,
                "error" => [
                    "error_message" => "Audios has empty"
                ]
            ], 404));
        }
        return $next($request);
    }
}
