<?php

namespace App\Http\Controllers\Content;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        return "OK";
    }
}
