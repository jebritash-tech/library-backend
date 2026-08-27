<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    protected $fillable = [
        'category_id',
        'title',
        'author',
        'isbn',
        'publish_year',
        'total_copies',
        'available_copies',
        'cover_image',
    ];

    // الكتاب ينتمي لفئة واحدة
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // الكتاب يمكن أن يتم استعارته في عدة عمليات
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
