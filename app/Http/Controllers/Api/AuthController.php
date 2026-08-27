<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    //
    // تسجيل الدخول
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        $user = Auth::user();
        
        // التحقق من أن الحساب مفعل
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'هذا الحساب موقوف من قبل الإدارة'
            ], 403);
        }

        // إنشاء Token خاص بالـ SPA
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // جلب بيانات المستخدم الحالي
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}
