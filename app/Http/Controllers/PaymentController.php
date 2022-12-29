<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPaymentRequest;
use App\Services\Contracts\PaymentService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    public function validateOrder(OrderPaymentRequest $request)
    {
        return response()->json(['success' => true]);
    }

    public function createOrder(OrderPaymentRequest $request)
    {
        return $this->paymentService->setOrderPayment($request->amount, $request->currency);
    }

    public function processOrder(Request $request, OrderService $orderService)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('payerId');
        $result = $this->paymentService->processPayment($paymentId, $payerId);
        if ($result) {
            $orderService->recordOrder([
                'order_id' => $request->get('orderId'),
                'payment_id' => $paymentId,
                'payer_email' => $result->payer->payer_info->email,
                'amount' => $result->transactions[0]->amount->total,
                'currency' => $result->transactions[0]->amount->currency,
                'payment_status' => $result->state
            ]);
        }
        return $result;
    }
    
    public function showComplete(Request $request, OrderService $orderService)
    {
        $orderId = $request->get('order_id');
        
        abort_unless($orderService->isValidOrder($orderId), 404);
        
        return view('order.complete');
    }
}
