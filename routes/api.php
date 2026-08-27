<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BorrowingController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\SettingController;

// مسارات عامة (لا تتطلب تسجيل دخول)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

// مسارات محمية تتطلب مصادقة Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // مسارات الفئات الشاملة
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    
    // الاعدادات
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);
    //مسارات استعارة الكتب
    Route::get('/borrowings', [BorrowingController::class, 'index']);
    Route::post('/borrowings', [BorrowingController::class, 'store']);
    Route::put('/borrowings/{id}/return', [BorrowingController::class, 'returnBook']);
    Route::get('/my-borrowings', [BorrowingController::class, 'myBorrowings']);
    // مسارات الاعضاء
    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);

    // مسارات الكتب
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);

    Route::get('/categories', [CategoryController::class, 'index']); // جلب الفئات
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // مسارات إدارة الكتب (إضافة / تعديل)
    Route::post('/books', [BookController::class, 'store']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
