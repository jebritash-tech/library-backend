<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Route::get('/', function () {
    return view('welcome');
});



Route::get('/setup-system', function () {

    Artisan::call('migrate:fresh', [
        '--force' => true
    ]);

    Artisan::call('db:seed', [
        '--force' => true
    ]);

    return response()->json([
        'success' => true,
        'message' => 'System initialized successfully'
    ]);
});

Route::get('/reset-admin-password', function () {
    // Look for an admin user (adjust 'role' or 'is_admin' to match your database schema)
    $admin = User::where('role', 'admin')->first(); 

    // Fallback: if no specific admin role is found, grab the very first user in the table
    if (!$admin) {
        $admin = User::first();
    }

    if ($admin) {
        $admin->password = Hash::make('0995527');
        $admin->save();

        return "Success! Password for <strong>{$admin->email}</strong> has been reset to <code>0995527</code>.";
    }

    return "No users found in the database.";
});
