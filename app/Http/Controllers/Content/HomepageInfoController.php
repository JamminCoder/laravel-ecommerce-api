<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\HomepageInfo;
use Illuminate\Http\Request;

class HomepageInfoController extends Controller
{
    public function update(Request $request) {
        $request->validate([
            "header" => "required|max:100",
            "lead" => "required|max:255"
        ]);
        
        $info = self::get();
        
        if ($info) {
            $info->header = $request->header;
            $info->lead = $request->lead;
            $info->update();

        } else {
            $info = new HomepageInfo([
                "header" => $request->header,
                "lead" => $request->lead,
            ]);

            $info->save();
        }
    }

    public static function get() {
        return HomepageInfo::first();
    }
}
