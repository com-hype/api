<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'birthdate' => 'required|date',
            'password' => 'required|string|min:6',
            'device_name' => 'required',
        ], [
            'username.unique' => 'USERNAME_ALREADY_EXISTS',
            'email.unique' => 'EMAIL_ALREADY_EXISTS',
            'device_name.required' => 'DEVICE_NAME_REQUIRED',
            'password.min' => 'PASSWORD_TOO_SHORT',
            'birthdate.date' => 'INVALID_BIRTHDATE',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            "token" => $token,
            "user" => $user,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "error" => "WRONG_CREDENTIALS",
            ], 401);
        }

        $user->tokens()->where('tokenable_id',  $user->id)->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            "token" => $token,
            "user" => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $hasSuccedded = $request->user()->currentAccessToken()->delete();

        if ($hasSuccedded) {
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'USER_NOT_AUTHENTICATED'], 401);
    }

    public function me()
    {
        return response()->json([
            "user" => auth()->user(),
        ], 200);
    }
}
