<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;

class CheckVideoFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next): Response
    {
        $videos = Storage::files('public/video');
        $allowedExtensions = ['mp4', 'mov', 'mkv', 'mpeg'];
        $videos = array_filter($videos, function ($file) use ($allowedExtensions) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            return in_array($extension, $allowedExtensions);
        });
        if (!$videos && empty($videos)) {
            throw new HttpResponseException(
                response()->json(
                    [
                        'status' => 'Not Found',
                        'data' => null,
                        'error' => [
                            'error_message' => 'Files has empty',
                        ],
                    ],
                    404,
                ),
            );
        }
        return $next($request);
    }
}
