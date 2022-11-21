<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class SettingController extends Controller
{
    public function interestCode()
    {
        $settings = Setting::where("setting_code", "interest_code")->first();
    }
}
