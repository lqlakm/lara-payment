<?php

namespace App\Services;

use App\Services\Contracts\PaymentService;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential as PaypalCredential;
use PayPal\Rest\ApiContext as PaypalApiContext;

class PaypalPaymentService implements PaymentService
{
    protected $apiContext;

    public function __construct()
    {
        $this->apiContext = new PaypalApiContext(
            new PaypalCredential(config('paypal.client_id'), config('paypal.client_secret'))
        );
    }

    public function setOrderPayment($totalAmount, $currency)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setCurrency($currency)->setTotal($totalAmount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Order Paid with Paypal.")
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://order-paypal.test")
            ->setCancelUrl("http://order-paypal.test");

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try {
            $payment->create($this->apiContext);
        } catch (\Exception $e) {
            logger()->error($e);
            return null;
        }
        return $payment;
    }

    public function processPayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->apiContext);
        } catch (\Exception $e) {
            logger()->error($e);
            return null;
        }
        return $result;
    }
}
