<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderId',
        'userDetails',
        'cart',
        'paymentMethod',
        'selectedBank',
        'subtotal',
        'deliveryCharges',
        'grandTotal',
        'status',
        'image',  // Include image in the fillable attributes
    ];

    protected $casts = [
        'userDetails' => 'array',  // Cast userDetails as an array
        'cart' => 'array',  // Cast cart as an array
    ];
}
