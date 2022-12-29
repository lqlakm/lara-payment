<?php

namespace App\Services\Contracts;

interface PaymentService
{
    public function setOrderPayment($totalAmount, $currency);

    public function processPayment($paymentId, $payerId);
}