<?php

namespace App\Http\Requests;

class ProductRequest extends ApiRequest
{
    public function rules(): array
    {
        $presence = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => [$presence, 'required', 'string', 'max:255'],
            'description' => [$presence, 'required', 'string', 'max:5000'],
            'price_cents' => [$presence, 'required', 'integer', 'min:50', 'max:1000000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
