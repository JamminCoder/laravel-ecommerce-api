<?php

namespace app\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\HomepageSlide;
use App\Models\HomepageSlideImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomepageSlideController extends Controller
{
    public function all() {
        $slides = HomepageSlide::all();
        
        foreach ($slides as $slide) {
            $slide->image_path = $slide->getImagePath();
        }

        return $slides;
    }


    public function new(Request $request) {
        $request->validate([
            "image.*" => "required|image|mimes:png,jpg,jpeg|max:5120",
            "image.*files" => "required|image|mimes:png,jpg,jpeg|max:5120",
            "header" => "required|max:24",
            "lead" => "max:255",
        ]);

        $header = $request->header;
        $lead = $request->lead;
        $image_name = Str::random();
        

        $slide = new HomepageSlide([
            "header" => $request->header,
            "lead" => $request->lead,
            "buttons" => $request->buttons,
        ]);

        $slide_image = new HomepageSlideImage(["image_name" => $image_name]);


        $slide->save();

        $slide->image()->save($slide_image);


        
        $request->image->move("slide_images", $image_name);

        return [
            "header" => $header,
            "lead" => $lead,
            "image" => $request->image,
            "buttons" => json_decode($request->buttons),
        ];
    }
}
