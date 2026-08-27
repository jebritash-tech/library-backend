<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name', 'description'];

    // الفئة تحتوى على عدة كتب
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
