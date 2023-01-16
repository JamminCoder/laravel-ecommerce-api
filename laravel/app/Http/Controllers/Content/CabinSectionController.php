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
            "iframe_url" => "required"
        ]);

        
        if (!CabinSection::first()) {
            $cabin_section = new CabinSection();
            $cabin_section->iframe_url = $request->iframe_url;
            $cabin_section->save();
            
            return "Created new cabin section";
        }

        // Update the cabin section
        $cabin_section = CabinSection::first();
        $cabin_section->iframe_url = $request->iframe_url;
        $cabin_section->save();

        return "Updated cabin section";
    }

    public function get() {
        $result = CabinSection::first();
        return $result ? $result->iframe_url : null;
    }
}
