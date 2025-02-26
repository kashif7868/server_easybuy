<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'discount_price',
        'discount_percentage',
        'rating',
        'reviews',
        'description',
        'images',  // Main image
        'additional_images',  // Multiple additional images
        'color',
        'brand',
        'meter',
        'size',
        'items_stock',
        'category_id',
        'subcategory_id',
        'small_category_id',
    ];

    protected $casts = [
        'images' => 'array',  // Main image as an array
        'additional_images' => 'array',  // Additional images as an array
    ];

    // Define the relationship with the Category model
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define the relationship with the Subcategory model
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    // Define the relationship with the SmallCategory model
    public function smallCategory()
    {
        return $this->belongsTo(SmallCategory::class);
    }

    // Method to calculate discount percentage
    public function calculateDiscountPercentage()
    {
        return (($this->price - $this->discount_price) / $this->price) * 100;
    }
}
