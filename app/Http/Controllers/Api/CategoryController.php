<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // جلب جميع الفئات
    public function index()
    {
        return response()->json(Category::withCount('books')->latest()->get(), 200);
    }

    // إضافة فئة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'تم إضافة الفئة بنجاح',
            'category' => $category
        ], 201);
    }

    // تحديث فئة
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'تم تحديث الفئة بنجاح',
            'category' => $category
        ], 200);
    }

    // حذف فئة
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // التأكد من عدم وجود كتب مرتبطة بهذه الفئة قبل الحذف (اختياري للأمان)
        if ($category->books()->count() > 0) {
            return response()->json(['message' => 'لا يمكن حذف الفئة لوجود كتب مرتبطة بها'], 400);
        }

        $category->delete();

        return response()->json(['message' => 'تم حذف الفئة بنجاح'], 200);
    }
}