<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    // عرض قائمة الأعضاء والطلاب
    public function index()
    {
        $members = User::where('role', '!=', 'admin')->get(); // استثناء المشرفين أو عرضهم حسب الرغبة
        return response()->json($members, 200);
    }

    // إضافة عضو أو طالب جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
            'status' => 'required|in:active,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'student'; // أو عضو

        $member = User::create($validated);

        return response()->json([
            'message' => 'تم إضافة العضو بنجاح',
            'member' => $member
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $member = User::find($id);
        if (!$member) {
            return response()->json(['message' => 'العضو غير موجود'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string',
            'status' => 'required|in:active,suspended',
            'password' => 'nullable|string|min:6', // كلمة المرور اختياري عند التعديل
        ]);

        // إذا كتب كلمة مرور جديدة يتم تشفيرها، وإلا يتم الاحتفاظ بالقديمة
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $member->update($validated);

        return response()->json([
            'message' => 'تم تحديث بيانات العضو بنجاح',
            'member' => $member
        ], 200);
    }

    // حذف عضو
    public function destroy($id)
    {
        $member = User::find($id);
        if (!$member) {
            return response()->json(['message' => 'العضو غير موجود'], 404);
        }

        $member->delete();
        return response()->json(['message' => 'تم حذف العضو بنجاح'], 200);
    }
}