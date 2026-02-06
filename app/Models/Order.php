<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_lastname',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_country', // Renamed from 'country' to 'customer_country' for clarity and consistency
        'company_name',
        'payment_method',
        'total_amount',
        'status',
    ];

    // Define relationship with OrderItem
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
