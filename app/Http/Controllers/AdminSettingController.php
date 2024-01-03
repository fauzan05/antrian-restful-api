<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetOperationalHoursRequest;
use App\Http\Requests\VideoUploadRequest;
use App\Models\AdminSetting;
use App\Models\OperationalHours;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function setVideoDisplay(VideoUploadRequest $request)
    {
        $request->validated();
        $originalFilename = $request->file('video_file')->getClientOriginalName();
        $originalFilename = preg_replace('/[ #%&?=+]/', "_", $originalFilename);
        $request->file('video_file')->storeAs('public/video', $originalFilename);
        // dd($result);
        $firstData = AdminSetting::first();
        if(!$firstData){
            AdminSetting::create([
                'selected_video' => $originalFilename
            ]);
        }else{
            AdminSetting::where('id', $firstData->id)->update([
                'selected_video' => $originalFilename
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => $originalFilename,
            "error" => null
        ]);
    }

    public function getSelectedVideo()
    {
        $adminSettings = AdminSetting::first();
        return response()->json([
            "status" => "OK",
            "data" => $adminSettings->selected_video,
            "error" => null
        ]);
    }

    public function setOperationalHours(SetOperationalHoursRequest $request)
    {
        $data = $request->validated();
        $operationalHours = OperationalHours::where('id', $data['id'])->update([
            'open' => $data['open'],
            'close' => $data['close'],
            'is_active' => $data['is_active']
        ]);
        $operationalHours = OperationalHours::where('id', $data['id'])->first();
        return response()->json([
            "status" => "OK",
            "data" => $operationalHours,
            "error" => null
        ]);   
    }

    public function setIdentityOfInstitute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_of_health_institute' => 'required|string|max:50',
            'address_of_health_institute' => 'required|string|max:100',
        ])->validate();
        
        $adminSettings = AdminSetting::select('id')->first();
        if(!$adminSettings){
            $adminSettings = AdminSetting::create([
                'name_of_health_institute' => $validator['name_of_health_institute'],
                'address_of_health_institute' => $validator['address_of_health_institute']
            ]);
        }else{
            $adminSettings = AdminSetting::where('id', $adminSettings->id)->update([
                'name_of_health_institute' => $validator['name_of_health_institute'],
                'address_of_health_institute' => $validator['address_of_health_institute']
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => AdminSetting::first(),
            "error" => null
        ]); 
    }

    public function setTextFooterDisplay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text_footer_display' => 'required|string|max:100',
        ])->validate();

        $adminSettings = AdminSetting::select('id')->first();
        if(!$adminSettings)
        {
            $adminSettings = AdminSetting::create([
                'text_footer_display' => $validator['text_footer_display']
            ]);
        }else{
            $adminSettings = AdminSetting::where('id', $adminSettings->id)->update([
                'text_footer_display' => $validator['text_footer_display']
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => AdminSetting::first(),
            "error" => null
        ]);
    }

    public function setColorFooterDisplay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'display_footer_color' => 'required|string|max:10',
        ])->validate();

        $adminSettings = AdminSetting::select('id')->first();
        if(!$adminSettings)
        {
            $adminSettings = AdminSetting::create([
                'display_footer_color' => $validator['display_footer_color']
            ]);
        }else{
            $adminSettings = AdminSetting::where('id', $adminSettings->id)->update([
                'display_footer_color' => $validator['display_footer_color']
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => AdminSetting::first(),
            "error" => null
        ]);
    }

    public function showAllSettings()
    {
        return response()->json([
            "status" => "OK",
            "data" => AdminSetting::first(),
            "error" => null
        ]);
    }

    public function deleteAllOperationalHours()
    {
        $operationalHours = OperationalHours::all();
        foreach($operationalHours as $key => $item):
            OperationalHours::where('id', $item->id)->update([
                'open' => null,
                'close' => null,
                'is_active' => false
            ]);
        endforeach;
        return response()->json([
            "status" => "OK",
            "data" => OperationalHours::all(),
            "error" => null
        ]);
    }

    public function showOperationalHours()
    {
        return response()->json([
            "status" => "OK",
            "data" => OperationalHours::all(),
            "error" => null
        ]);  
    }
}
