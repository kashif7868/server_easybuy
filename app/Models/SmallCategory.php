<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmallCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'small_category_name',
        'category_id',
        'subcategory_id',
    ];

    // Define the relationship with the Category model
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Define the relationship with the Subcategory model
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
}

