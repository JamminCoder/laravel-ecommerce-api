<?php

namespace app\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomepageSlideController extends Controller
{
    public function new(Request $request) {
        $request->validate([
            "image.*" => "required|image|mimes:png,jpg,jpeg|max:5120",
            "image.*files" => "required|image|mimes:png,jpg,jpeg|max:5120",
            "header" => "required|max:24",
            "lead" => "max:255",

            "btn_1_text" => "max:24",
            "btn_1_link" => "max:255",
            
            "btn_2_text" => "max:24",
            "btn_2_link" => "max:255",
        ]);

        $header = $request->header;
        $lead = $request->lead;
        $image = $request->image;
        $image_name = Str::random();
        $request->image->move("slides", $image_name);

        return [
            "header" => $header,
            "lead" => $lead,
            "image" => $image
        ];
    }
}
