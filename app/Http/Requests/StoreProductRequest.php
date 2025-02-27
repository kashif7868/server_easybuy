<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization logic if needed
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'discount_price' => 'required|numeric',
            'rating' => 'required|integer',
            'reviews' => 'required|integer',
            'description' => 'required|string',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Main image
            'additional_images' => 'nullable|array',  // Additional images
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validate additional images
            'color' => 'required|string',
            'brand' => 'required|string',
            'meter' => 'required|numeric',
            'size' => 'required|string',
            'items_stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'small_category_id' => 'required|exists:small_categories,id',
            'featured' => 'nullable|boolean',  // Featured product
            'deal_of_the_day' => 'nullable|boolean',  // Deal of the Day product
            'best_seller' => 'nullable|boolean',  // Best Seller product
            'top_offer_product' => 'nullable|boolean',  // Top Offer product
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'price.required' => 'Product price is required.',
            'discount_price.required' => 'Discount price is required.',
            'images.required' => 'Main image is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'subcategory_id.exists' => 'The selected subcategory does not exist.',
            'small_category_id.exists' => 'The selected small category does not exist.',
        ];
    }
}
