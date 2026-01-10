<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachSupplierToProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'string', 'exists:suppliers,id'],
            'supplier_sku' => ['nullable', 'string', 'max:255'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'min_order_quantity' => ['required', 'integer', 'min:1'],
            'is_preferred' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
