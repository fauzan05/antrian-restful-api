<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoUploadRequest;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileController extends Controller
{
    public function showAllAudios()
    {
        $audios = Storage::files('public/audio');
        return response()->json([
            'status' => 'OK',
            'data' => $audios,
            'error' => null
        ]);
    }

    public function getAudio(string $filename): BinaryFileResponse
    {
        $url_storage = Storage::path('public/audio/' . $filename . '.mp3');
        return response()->file($url_storage);
    }

    // public function uploadedVideo(VideoUploadRequest $request)
    // {
    //     $request->validated();
    //     $result = $request->file('video_file')->store('public/video');
    //     return response()->json([
    //         "status" => "OK",
    //         "data" => $result,
    //         "error" => null
    //     ]);
    // }

    // public function deleteAllVideo()
    // {
    //     $videos = Storage::files('public/video');
    //     $allowedExtensions = ['mp4', 'mov', 'mkv', 'mpeg', 'MP4', 'MOV', 'MKV', 'MPEG'];
    //     $videos = array_filter($videos, function ($file) use ($allowedExtensions) {
    //         $extension = pathinfo($file, PATHINFO_EXTENSION);
    //         return in_array($extension, $allowedExtensions);
    //     });
    //     for($i = 1; $i <= count($videos); $i++)
    //     {
    //         Storage::delete($videos[$i]);
    //     }
    //     return response()->json([
    //         "status" => "OK",
    //         "data" => null,
    //         "error" => null
    //     ]);
    // }

    // public function showAllVideos()
    // {
    //     $videos = Storage::files('public/video');
    //     $allowedExtensions = ['mp4', 'mov', 'mkv', 'mpeg'];
    //     $videos = array_filter($videos, function ($file) use ($allowedExtensions) {
    //         $extension = pathinfo($file, PATHINFO_EXTENSION);
    //         return in_array($extension, $allowedExtensions);
    //     });
    //     return response()->json([
    //         'status' => 'OK',
    //         'data' => $videos,
    //         'error' => null
    //     ]);
    // }

    // public function getVideo()
    // {
    //     $filename = AppSetting::first();
    //     if(!$filename){
    //         throw new HttpResponseException(
    //             response()->json(
    //                 [
    //                     'status' => 'Not Found',
    //                     'data' => null,
    //                     'error' => [
    //                         'error_message' => 'Video has not been set',
    //                     ],
    //                 ],
    //                 404,
    //             ),
    //         );
    //     }
    //     // dd(Storage::exists('public/video/' . $filename->selected_video));
    //     if (Storage::exists('public/video/' . $filename->selected_video) == false) {
    //         throw new HttpResponseException(
    //             response()->json(
    //                 [
    //                     'status' => 'Not Found',
    //                     'data' => null,
    //                     'error' => [
    //                         'error_message' => 'Video has empty',
    //                     ],
    //                 ],
    //                 404,
    //             ),
    //         );
    //     }
    //     $url_storage = Storage::path('public/video/' . $filename->selected_video);
    //     // dd($url_storage);
    //     return response()->file($url_storage);
    // }
}
