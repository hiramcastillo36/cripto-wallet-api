<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cryptocurrency_id' => 'required|integer|exists:cryptocurrencies,id',
            'amount_crypto' => 'required|numeric|min:0.00000001',
            'amount_usd' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,wallet',
        ];
    }

    public function messages(): array
    {
        return [
            'cryptocurrency_id.required' => 'La criptomoneda es requerida',
            'cryptocurrency_id.exists' => 'La criptomoneda seleccionada no existe',
            'amount_crypto.required' => 'La cantidad de criptomoneda es requerida',
            'amount_crypto.numeric' => 'La cantidad debe ser un número válido',
            'amount_crypto.min' => 'La cantidad debe ser mayor a 0',
            'amount_usd.required' => 'El monto en USD es requerido',
            'amount_usd.numeric' => 'El monto debe ser un número válido',
            'amount_usd.min' => 'El monto mínimo es de $0.01',
            'payment_method.required' => 'El método de pago es requerido',
            'payment_method.in' => 'El método de pago seleccionado no es válido',
        ];
    }
}
