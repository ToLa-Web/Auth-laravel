<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Register API (name, email, password, confirm_password)
    public function register(Request $request) {

        $data = $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required"
        ]);

        Try {
           $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']), 
            'password' => Hash::make($data['password'])         
           ]);
        
            return response()->json([
                'status' => true,
                'message' => 'User register successfully',
                'data' => $user
            ],201);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to register',
                'error' => $e->getMessage()
            ],500);
        }
    }

    //Login API (email, password)
    public function login(Request $request) {

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if(!Auth::attempt($request->only("email", "password"))) {
            return response()->json([
                "status" => false,
                "message" => "Invalid credentials",
            ],401);
        }

        $user = Auth::user();

        $token = $user->createToken("myToken")->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "User logged in",
            "token" => $token
        ]);

    }

    //Profile API 
    public function profile() {

        $user = Auth::user();

        return response()->json([
            "staus" => true,
            "message" => "User profile data",
            "user" => $user
        ]);

    }

    //Logout API 
    public function logout() {

        Auth::logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
        
    }
}
