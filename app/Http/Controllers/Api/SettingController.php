<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::pluck('value', 'key');
        $admin = $request->user(); // المشرف المسجل حالياً

        return response()->json([
            'settings' => $settings,
            'admin' => [
                'name' => $admin->name,
                'email' => $admin->email
            ]
        ], 200);
    }

    public function update(Request $request)
    {
        $data = $request->except(['current_password', 'new_password', 'admin_name', 'admin_email']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // تحديث اسم وبريد المشرف إذا تم تعديلها
        $admin = $request->user();
        if ($request->has('admin_name') || $request->has('admin_email')) {
            $admin->update([
                'name' => $request->input('admin_name', $admin->name),
                'email' => $request->input('admin_email', $admin->email),
            ]);
        }

        // تحديث كلمة المرور إذا طلبت
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return response()->json(['message' => 'كلمة المرور الحالية غير صحيحة'], 400);
            }
            $admin->update(['password' => Hash::make($request->new_password)]);
        }

        return response()->json(['message' => 'تم حفظ الإعدادات بنجاح'], 200);
    }
}