<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class ApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'integer' => 'El campo :attribute debe ser un numero entero.',
            'email' => 'El campo :attribute debe ser un correo valido.',
            'unique' => 'El valor de :attribute ya esta registrado.',
            'confirmed' => 'La confirmacion de :attribute no coincide.',
            'min' => 'El campo :attribute no cumple el minimo permitido.',
            'max' => 'El campo :attribute supera el maximo permitido.',
            'array' => 'El campo :attribute debe ser una lista.',
            'distinct' => 'Los productos de la orden no deben repetirse.',
            'exists' => 'El producto seleccionado no existe.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
        ];
    }
}
