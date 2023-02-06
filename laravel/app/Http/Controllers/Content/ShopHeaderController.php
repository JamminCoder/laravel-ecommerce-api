<?php

namespace App\Http\Controllers\Content;

use App\Models\ShopHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FilesController;

class ShopHeaderController extends Controller
{
    public function save(Request $request) {
        $request->validate([
            "header_text" => "required|max:72",
            "lead_text" => "max:255",
            "image.*" => "required|image|mimes:png,jpg,jpeg",
            "image.*files" => "required|image|mimes:png,jpg,jpeg",
        ]);

        $shop_header = ShopHeader::first();

        $image_name = Str::random();
        $request->image->move(ShopHeader::IMAGE_DIR, $image_name);

        $shop_header = new ShopHeader([
            "header" => $request->header_text,
            "lead" => isset($request->lead_text) ? $request->lead_text : "",
            "image_path" => "images/$image_name",
        ]);

        $shop_header->save();

        return "Created new shop header";
    }

    public function update(Request $request) {
        $request->validate([
            "header_text" => "required|max:72",
            "lead_text" => "max:255",
            "image.*" => "image|mimes:png,jpg,jpeg",
            "image.*files" => "image|mimes:png,jpg,jpeg",
        ]);

        $shop_header = ShopHeader::first();

        if (!$shop_header) return $this->save($request);

        
        $shop_header->header = $request->header_text;
        $shop_header->lead = isset($request->lead_text) ? $request->lead_text : "";
        

        if (isset($request->image)) {
            $old_image_path = $shop_header->image_path;
            $new_image_path = ShopHeader::IMAGE_DIR . "/" . Str::random();
            $request->image->move(ShopHeader::IMAGE_DIR, Str::random());
            $shop_header->image_path = $new_image_path;

            FilesController::delete($old_image_path);
        }
        

        $shop_header->save();

        return "Updated shop header";
    }

    public static function get() {
        return ShopHeader::first();
    }
}
