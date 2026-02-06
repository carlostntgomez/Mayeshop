<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
    ];

    // Define relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Define relationship with Product (optional, for current product details)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
