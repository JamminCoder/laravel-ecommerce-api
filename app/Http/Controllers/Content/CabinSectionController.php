<?php

namespace App\Http\Controllers\Content;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FilesController;
use App\Models\CabinSection;
use Illuminate\Support\Str;

class CabinSectionController extends Controller
{
    public function update(Request $request) {
        $request->validate([
            "image.*" => "required|image|mimes:png,jpg,jpeg|max:5120",
            "image.*files" => "required|image|mimes:png,jpg,jpeg|max:5120",
            "header" => "required|max:32",
            "lead" => "required|max:255",
            "link_text" => "required",
            "href" => "required",
        ]);

        
        if (!CabinSection::first()) {
            // Create new cabin section

            $image_name = Str::random();

            $cabin_section = new CabinSection([
                "header" => $request->header,
                "lead" => $request->lead,
                "link_text" => $request->link_text,
                "href" => $request->href,
                "image_path" => "images/$image_name",
            ]);
            
            $request->image->move("images", $image_name);

            $cabin_section->save();
            
            return "Created new cabin section";
        }

        // Update the cabin section
        $cabin_section = CabinSection::first();

        $cabin_section->header = $request->header;
        $cabin_section->lead = $request->lead;
        $cabin_section->link_text = $request->link_text;
        $cabin_section->href = $request->href;

        $old_image_path = $cabin_section->image_path;
        FilesController::deletePublic($old_image_path);
        
        $image_name = Str::random();
        $cabin_section->image_path = "images/$image_name";
        $request->image->move("images", $image_name);

        $cabin_section->save();

        return "Updated cabin section";
    }
}
