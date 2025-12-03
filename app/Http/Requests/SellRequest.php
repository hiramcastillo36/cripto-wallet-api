<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
            'price_usd' => 'required|numeric|min:0.01',
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
            'price_usd.required' => 'El precio en USD es requerido',
            'price_usd.numeric' => 'El precio debe ser un número válido',
            'price_usd.min' => 'El precio mínimo es de $0.01',
        ];
    }
}
