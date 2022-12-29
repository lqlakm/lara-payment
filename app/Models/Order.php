<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'payment_id',
        'payer_email',
        'amount',
        'currency',
        'payment_status'
    ];
}
