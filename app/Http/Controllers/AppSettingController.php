<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetHeaderFooterColorAndTextRequest;
use App\Http\Requests\SetIdentityOfInstituteRequest;
use App\Http\Requests\SetLogoRequest;
use App\Http\Requests\SetOperationalHoursRequest;
use App\Http\Requests\SetVideoRequest;
use App\Models\AppSetting;
use App\Models\OperationalHours;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function setVideoDisplay(SetVideoRequest $request)
    {
        $data = $request->validated();
        // dd($result);
        $firstData = AppSetting::first();
        if (!$firstData) {
            AppSetting::create([
                'selected_video' => $data['video_filename']
            ]);
        } else {
            AppSetting::where('id', $firstData->id)->update([
                'selected_video' => $data['video_filename']
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => $data['video_filename'],
            "error" => null
        ]);
    }

    public function setLogo(SetLogoRequest $request)
    {
        $data = $request->validated();
        $firstData = AppSetting::first();
        if (!$firstData) {
            AppSetting::create([
                'selected_logo' => $data['logo_filename']
            ]);
        } else {
            AppSetting::where('id', $firstData->id)->update([
                'selected_logo' => $data['logo_filename']
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }

    public function deleteLogo()
    {
        $AppSetting = AppSetting::first();
        if ($AppSetting) {
            if ($AppSetting->selected_logo) {
                $AppSetting->update([
                    'selected_logo' => null
                ]);
            }
        }
        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }

    public function deleteVideo()
    {
        $AppSetting = AppSetting::first();
        if ($AppSetting) {
            if (!empty($AppSetting->selected_video)) {
                $AppSetting->update([
                    'selected_video' => null
                ]);
            }
        }
        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }

    public function setOperationalHours(SetOperationalHoursRequest $request)
    {
        $data = $request->validated();
        $data = reset($data);
        foreach ($data as $key => $item) :
            OperationalHours::where('id', $key)->update([
                'open' => $item[0],
                'close' => $item[1],
                'is_active' => $item[2]
            ]);
        endforeach;
        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }

    public function setIdentityOfInstitute(SetIdentityOfInstituteRequest $request)
    {
        $validator = $request->validated();
        $AppSettings = AppSetting::first();

        if (!$AppSettings) {
            $AppSettings = AppSetting::create([
                'name_of_health_institute' => $validator['name_of_health_institute'],
                'address_of_health_institute' => $validator['address_of_health_institute']
            ]);
        } else {
            $AppSettings->update([
                'name_of_health_institute' => $validator['name_of_health_institute'],
                'address_of_health_institute' => $validator['address_of_health_institute']
            ]);
        }

        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }


    public function setTextFooterDisplay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text_footer_display' => 'required|string|max:100',
        ])->validate();

        $AppSettings = AppSetting::select('id')->first();
        if (!$AppSettings) {
            $AppSettings = AppSetting::create([
                'text_footer_display' => $validator['text_footer_display']
            ]);
        } else {
            $AppSettings = AppSetting::where('id', $AppSettings->id)->update([
                'text_footer_display' => $validator['text_footer_display']
            ]);
        }
        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }

    public function setColorHeaderFooter(SetHeaderFooterColorAndTextRequest $request)
    {
        $validator = $request->validated();
        $AppSettings = AppSetting::select('id')->first();

        if (!$AppSettings) {
            $AppSettings = AppSetting::create([
                'header_color' => $validator['header_color'],
                'footer_color' => $validator['footer_color'],
                'text_header_color' => $validator['text_header_color'],
                'text_footer_color' => $validator['text_footer_color'],
            ]);
        } else {
            $AppSettings->update([
                'header_color' => $validator['header_color'],
                'footer_color' => $validator['footer_color'],
                'text_header_color' => $validator['text_header_color'],
                'text_footer_color' => $validator['text_footer_color'],
            ]);
        }

        return response()->json([
            "status" => "OK",
            "data" => null,
            "error" => null
        ]);
    }

    public function showAllSettings()
    {
        return response()->json([
            "status" => "OK",
            "data" => AppSetting::first(),
            "error" => null
        ]);
    }

    public function deleteAllOperationalHours()
    {
        $operationalHours = OperationalHours::all();
        foreach ($operationalHours as $key => $item) :
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
        $operationalHours = OperationalHours::count();
        // jika hari dalam database operational hours tidak ada, maka buat dulu
        if ($operationalHours < 7) {
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            for ($i = 0; $i < 7; $i++) {
                $operationalHours = new OperationalHours();
                $operationalHours->days = $days[$i];
                $operationalHours->save();
            }
        }
        return response()->json([
            "status" => "OK",
            "data" => OperationalHours::get(),
            "error" => null
        ]);
    }
}
