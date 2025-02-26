<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderId')->unique();
            $table->json('userDetails');  // Store user details as JSON
            $table->json('cart');  // Store the cart (products) as JSON
            $table->string('paymentMethod');
            $table->string('selectedBank')->nullable();  // Store the selected bank for payment
            $table->decimal('subtotal', 10, 2);
            $table->decimal('deliveryCharges', 10, 2);
            $table->decimal('grandTotal', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('image')->nullable();  // Add the image field to store the image path
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
