<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
class FileController extends Controller
{
    public function index()
    {
        $files = Storage::files('public/audio');
        $url_storage = Storage::path('public/audio/1.mp3');
        $url_public = public_path('/storage/audio');
        $isExist = File::exists($url_public);
        return response()->json([
            'status' => 'OK',
            'data' => $files,
            'url_storage' => $url_storage,
            'url_public' => $url_public,
            'isExist' => $isExist,
            'error' => null
        ]);
    }

    public function get(string $nameFile): BinaryFileResponse
    {
        $url_storage = Storage::path('public/audio/' . $nameFile . '.mp3');
        return response()->file($url_storage);
    }

}
