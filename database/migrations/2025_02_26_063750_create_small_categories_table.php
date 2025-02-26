<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmallCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('small_categories', function (Blueprint $table) {
            $table->id();
            $table->string('small_category_name');  // Name of the small category
            $table->unsignedBigInteger('category_id');  // Foreign key to categories table
            $table->unsignedBigInteger('subcategory_id');  // Foreign key to subcategories table
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('small_categories');
    }
}
