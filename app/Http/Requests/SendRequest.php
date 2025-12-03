<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
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
            'to_address' => 'required|string|min:26|max:42',
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
            'to_address.required' => 'La dirección de destino es requerida',
            'to_address.string' => 'La dirección debe ser una cadena de texto',
            'to_address.min' => 'La dirección debe tener al menos 26 caracteres',
            'to_address.max' => 'La dirección no puede exceder 42 caracteres',
        ];
    }
}
