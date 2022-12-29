<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class OrderPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|integer',
            'currency' => ['required', Rule::in(config('paypal.currency'))]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Form Validation Failed',
            'data' => $validator->errors()
        ], 400));
    }
}
