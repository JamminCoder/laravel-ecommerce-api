<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;

class ProductsController extends Controller
{
    public static function new(Request $request) {
        $request->validate([
            "images.*" => "required|mimes:png,jpg,jpeg",
            "images.*files" => "required|mimes:png,jpg,jpeg",
            "catagory" => "required|max:64",
            "name" => "required|unique:products|max:64",
            "description" => "required|max:255",
            "price" => "required",
        ]);

        $name = $request->name;
        $description = $request->description;
        $price = $request->price;

        $sku = Product::generateSKU($name);
        $catagory = $request->catagory;


        // Configure upload directory and allowed file types
        $upload_dir = public_path() . "/uploads/";
        $allowed_types = array('jpg', 'png', 'jpeg');
        
        // Checks if user sent an empty form
        if(!empty(array_filter($_FILES['images']['name']))) {
    
            // Loop through each file in files[] array
            foreach ($_FILES['images']['tmp_name'] as $key => $value) {
                
                $file_tmpname = $_FILES['images']['tmp_name'][$key];
                $file_name = $_FILES['images']['name'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    
                // Set upload file path
                $filepath = $upload_dir.$file_name;
    
                // Check file type is allowed or not
                if(in_array(strtolower($file_ext), $allowed_types)) {
    
                    // If file with name already exist then append time in
                    // front of name of the file to avoid overwriting of file
                    if(file_exists($filepath)) {
                        $filepath = $upload_dir.time().$file_name;
                        
                        if( move_uploaded_file($file_tmpname, $filepath)) {
                            echo "{$file_name} successfully uploaded <br />";
                        }
                        else {                    
                            echo "Error uploading {$file_name} <br />";
                        }
                    }
                    else {
                    
                        if( move_uploaded_file($file_tmpname, $filepath)) {
                            echo "{$file_name} successfully uploaded <br />";
                        }
                        else {                    
                            echo "Error uploading {$file_name} <br />";
                        }
                    }
                }
                else {
                    
                    // If file extension not valid
                    echo "Error uploading {$file_name} ";
                    echo "({$file_ext} file type is not allowed)<br / >";
                }
            }
        }
        else {
            
            // If no files selected
            echo "No files selected.";
        }

        return [
            "images" => "Not implemented.",
            "catagory" => $catagory,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "sku" => $sku,
        ];
    }
}
