<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    // عرض جميع الكتب مع الفئة الخاصة بها
    public function index()
    {
        $books = Book::with('category')->latest()->get();
        return response()->json($books);
    }

    // إضافة كتاب جديد (خاص بالمشرف أو أمين المكتبة)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'publish_year' => 'nullable|digits:4',
            'total_copies' => 'required|integer|min:1',
        ]);

        // تعيين النسخ المتاحة مساوية للنسخ الكلية عند الإنشاء لأول مرة
        $validated['available_copies'] = $validated['total_copies'];

        $book = Book::create($validated);

        return response()->json([
            'message' => 'تم إضافة الكتاب بنجاح',
            'book' => $book
        ], 201);
    }

    // عرض تفاصيل كتاب محدد
    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return response()->json($book);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => 'الكتاب غير موجود'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $id, // استثناء الكتاب الحالي من فحص الـ Unique
            'category_id' => 'required|exists:categories,id',
            'total_copies' => 'required|integer|min:1',
            'publish_year' => 'required|integer|digits:4',
        ]);

        // حساب الفارق في النسخ لتحديث النسخ المتاحة تلقائياً إن أمكن، أو تحديثها بناءً على المنطق
        $copiesDiff = $validated['total_copies'] - $book->total_copies;
        $validated['available_copies'] = max(0, $book->available_copies + $copiesDiff);

        $book->update($validated);

        return response()->json([
            'message' => 'تم تحديث بيانات الكتاب بنجاح',
            'book' => $book->load('category')
        ], 200);
    }

    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => 'الكتاب غير موجود'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'تم حذف الكتاب بنجاح'
        ], 200);
    }
}