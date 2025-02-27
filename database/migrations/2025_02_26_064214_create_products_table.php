<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Product name
            $table->decimal('price', 10, 2);  // Original price
            $table->decimal('discount_price', 10, 2);  // Discounted price
            $table->decimal('discount_percentage', 5, 2);  // Discount percentage
            $table->integer('rating');  // Product rating
            $table->integer('reviews');  // Number of reviews
            $table->text('description');  // Product description
            $table->json('images');  // Store main image as a single path
            $table->json('additional_images')->nullable();  // Store multiple additional images as an array of paths
            $table->string('color');  // Color of the product
            $table->string('brand');  // Brand of the product
            $table->decimal('meter', 10, 2);  // Meter or length of fabric
            $table->string('size');  // Size of the product
            $table->integer('items_stock');  // Items available in stock
            $table->unsignedBigInteger('category_id');  // Foreign key for category
            $table->unsignedBigInteger('subcategory_id');  // Foreign key for subcategory
            $table->unsignedBigInteger('small_category_id');  // Foreign key for small category
            $table->boolean('featured')->default(false);  // Featured product (default false)
            $table->boolean('deal_of_the_day')->default(false);  // Deal of the Day product (default false)
            $table->boolean('best_seller')->default(false);  // Best Seller product (default false)
            $table->boolean('top_offer_product')->default(false);  // Top Offer product (default false)
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('cascade');
            $table->foreign('small_category_id')->references('id')->on('small_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
