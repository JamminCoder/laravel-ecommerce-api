<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NewEmailController extends Controller
{
    public function update(Request $request) {
        $request->validate([
            "email" => ["required", "email"],
            "password" => "required"
        ]);

        if (!User::passwordMatchesCurrentUser($request->password)) return "Incorrect password.";
        
        $user = User::currentUser();
        $user->email = $request->email;
        $user->unverifyEmail();
        $user->save();

        return "Updated user's email";
    }
}
