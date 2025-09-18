<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|same:confirmed_password',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $input['name'] = $user->name;
        $input['email'] = $user->email;
        $input['token'] = $user->createToken('auth_token')->plainTextToken;

        return response()->json($input);
    }

    public function login(Request $request){
        sleep(1);
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $input['name'] = $user->name;
        $input['email'] = $user->email;
        $input['token'] = $user->createToken('auth_token')->plainTextToken;

        return response()->json($input);
    }

    public function profile(Request $request){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }
    public function updateProfile(Request $request){
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user->update($request->only('name', 'email'));

        return response()->json(['message' => 'Profile updated successfully']);
    }

}
