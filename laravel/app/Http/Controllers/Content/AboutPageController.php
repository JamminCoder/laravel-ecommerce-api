<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    public function update(Request $request) {
        $request->validate([
            "header" => "required|max:128",
            "body" => "required"
        ]);

        if ($existing_model = AboutPage::first()) {
            $existing_model->header = $request->header;
            $existing_model->body = $request->body;

            $existing_model->save();
            return "Updated";
        }

        $about_page = new AboutPage([
            "header" => $request->header,
            "body" => $request->body
        ]);

        $about_page->save();

        return "Saved";
    }


    public function get() {
        return AboutPage::first();
    }
}
