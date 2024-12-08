<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'cccd' => 'required',
            'password' => 'required',
        ]);

        $login = Login::where('cccd', $request->cccd)->first();

        if ($login && password_verify($request->password, $login->password)) {
            return response()->json(['message' => 'Login successful', 'role' => $login->role], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

}
