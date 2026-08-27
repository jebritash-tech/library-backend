<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء المستخدمين الأساسيين
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0912345678',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Librarian Ahmed',
            'email' => 'librarian@library.com',
            'password' => Hash::make('password'),
            'role' => 'librarian',
            'phone' => '0923456789',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Student Jebril',
            'email' => 'student@library.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '0991122334',
            'status' => 'active',
        ]);

        // 2. إنشاء الفئات (Categories)
        $cat1 = Category::create([
            'name' => 'علوم الحاسب والبرمجة',
            'description' => 'كتب تخص تطوير الويب، الذكاء الاصطناعي، وهندسة البرمجيات.'
        ]);

        $cat2 = Category::create([
            'name' => 'الشبكات وأمن المعلومات',
            'description' => 'كتب تخص شبكات الحاسوب وأمن السيرفرات.'
        ]);

        // 3. إنشاء الكتب (Books)
        Book::create([
            'category_id' => $cat1->id,
            'title' => 'Laravel Up & Running',
            'author' => 'Matt Stauffer',
            'isbn' => '978-1491936085',
            'publish_year' => 2019,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);

        Book::create([
            'category_id' => $cat1->id,
            'title' => 'Vue.js Up and Running',
            'author' => 'Callum Macrae',
            'isbn' => '978-1491997284',
            'publish_year' => 2018,
            'total_copies' => 3,
            'available_copies' => 3,
        ]);

        Book::create([
            'category_id' => $cat2->id,
            'title' => 'Computer Networking: A Top-Down Approach',
            'author' => 'James Kurose',
            'isbn' => '978-0133594140',
            'publish_year' => 2020,
            'total_copies' => 4,
            'available_copies' => 4,
        ]);
    }
}