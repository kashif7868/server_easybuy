<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization logic if needed
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'rating' => 'nullable|integer',
            'reviews' => 'nullable|integer',
            'description' => 'nullable|string',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1048576',  // Main image
            'additional_images' => 'nullable|array',  // Additional images
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1048576',  // Validate additional images
            'color' => 'nullable|string',
            'brand' => 'nullable|string',
            'meter' => 'nullable|numeric',
            'size' => 'nullable|string',
            'items_stock' => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'small_category_id' => 'nullable|exists:small_categories,id',
            'featured' => 'nullable|boolean',  // Featured product
            'deal_of_the_day' => 'nullable|boolean',  // Deal of the Day product
            'best_seller' => 'nullable|boolean',  // Best Seller product
            'top_offer_product' => 'nullable|boolean',  // Top Offer product
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Product name must be a string.',
            'price.numeric' => 'Price must be a number.',
            'discount_price.numeric' => 'Discount price must be a number.',
        ];
    }
}
