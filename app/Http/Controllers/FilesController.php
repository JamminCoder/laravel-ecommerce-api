<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    public static function uploadFilesFromRequest($request, $fieldName, $outputDir) {
        // Upload code from https://stackoverflow.com/a/42643349
        
        $uploads = array();
        if($files = $request->file($fieldName)) {
            foreach($files as $file){
                $name = Str::random();
                $file->move($outputDir, $name);
                $uploads[] = $name;
            }
        }

        return $uploads;
    }

    public static function delete($path) {
        $public_path = public_path($path);
        if (file_exists($public_path)) {
            unlink($public_path);
        }
    }
}
