<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'string', 'in:receive,dispatch,transfer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            
            'source_location_id' => [
                Rule::requiredIf(fn () => in_array($this->type, ['dispatch', 'transfer'])),
                'nullable',
                'integer',
                'exists:locations,id'
            ],

            'destination_location_id' => [
                Rule::requiredIf(fn () => in_array($this->type, ['receive', 'transfer'])),
                'nullable',
                'integer',
                'exists:locations,id',
                'different:source_location_id'
            ],
        ];
    }
}
