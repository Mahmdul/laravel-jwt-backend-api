<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    // Register API (POST, formdata). This is open means we dont want any token or any login values
    public function register(Request $request){ // To access formdata from our API's we need "request" object
        //Data validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        // Data save
        User::create([
            "name" => $request->name,
            "email" => $request->email, 
            "password" => Hash::make($request->password)
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "User created successfully"
        ]);

    }

    // Login API (POST, formdata). This is open means we dont want any token or any login values
    public function login(Request $request){
        //data validation
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        // JWTAuth and attempt method (By this method we will generate a valid token value)
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ]);

        // Response
        if(!empty($token)){
            return response()->json([
                "status" => true,
                "message" => "User Logged on Successfully",
                "token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid Login details"

        ]);
    }

    //Profile API (GET). While calling this API we need to pass authorization token value & that token value will be JWT. This method is protected means we need the concept of middleware
    public function profile(){
        $userData =  auth()->user();  
        
        return response()->json([
            "status" => true,
            "message" => "Profile Data",
            "user" => $userData,
        ]);
    }

    // Refresh token API (GET). While calling this API we need to pass authorization token value & that token value will be JWT. This method is protected means we need the concept of middleware. 

public function refreshToken(){
    $newToken = auth()->refresh();

    return response()->json([
        "status" => true,
        "message" => "New Access Token Generated for Refresh Token",
        "token" => $newToken

    ]);
}

// Logout API (GET). While calling this API we need to pass authorization token value & that token value will be JWT. This method is protected means we need the concept of middleware
public function logout(){
    auth()->logout();

    return response()->json([
        "status" => true,
        "message" => "User Logged Out Successfully"
    ]);
}

}
