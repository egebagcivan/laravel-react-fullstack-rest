<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        // catch the error
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['invalid credentials'],
                'errors' => [
                    'password' => ['These password credentials do not match our records.'],
                ]
            ], 422);
        }
        // Return the access token if the checking is successful
        return response()->json([
            'access_token' => $user->createToken('api-token')->plainTextToken,
            'type' => 'bearer',
        ], 200);
    }
}