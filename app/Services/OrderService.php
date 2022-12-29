<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function recordOrder($order)
    {
        return Order::create($order);
    }

    public function isValidOrder($orderId)
    {
        return Order::where('order_id', $orderId)
            ->where('payment_status', 'approved')
            ->exists();
    }
}
