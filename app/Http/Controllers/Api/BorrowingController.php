<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    // عرض جميع سجلات الاستعارة
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'book'])->latest()->get();
        return response()->json($borrowings, 200);
    }

    // تسجيل استعارة جديدة لطالب
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date|after:today',
        ]);

        $book = Book::find($validated['book_id']);

        // التحقق من توفر نسخ للكتاب
        if ($book->available_copies <= 0) {
            return response()->json(['message' => 'عذراً، لا توجد نسخ متاحة لهذا الكتاب حالياً'], 400);
        }

        // إنشاء سجل الاستعارة
        $borrowing = Borrowing::create([
            'user_id' => $validated['user_id'],
            'book_id' => $validated['book_id'],
            'borrow_date' => Carbon::now(),
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ]);

        // خصم نسخة واحدة من النسخ المتاحة
        $book->decrement('available_copies');

        return response()->json([
            'message' => 'تم تسجيل الاستعارة بنجاح',
            'borrowing' => $borrowing->load(['user', 'book'])
        ], 201);
    }

    // تسليم/إرجاع الكتاب
    public function returnBook($id)
    {
        $borrowing = Borrowing::find($id);
        if (!$borrowing || $borrowing->status === 'returned') {
            return response()->json(['message' => 'سجل الاستعارة غير موجود أو تم إرجاع الكتاب مسبقاً'], 400);
        }

        // تحديث حالة الاستعارة وتاريخ الإرجاع
        $borrowing->update([
            'status' => 'returned',
            'returned_date' => Carbon::now(),
        ]);

        // إعادة النسخة المتاحة للكتاب
        $book = Book::find($borrowing->book_id);
        if ($book) {
            $book->increment('available_copies');
        }

        return response()->json([
            'message' => 'تم إرجاع الكتاب بنجاح وتحديث المخزون',
            'borrowing' => $borrowing->load(['user', 'book'])
        ], 200);
    }

    // جلب استعارات الطالب الدخول حالياً
    
    public function myBorrowings(Request $request)
    {
        $user = $request->user(); // الطالب المسجل حالياً عبر الـ Token
        $borrowings = Borrowing::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($borrowings, 200);
    }
}