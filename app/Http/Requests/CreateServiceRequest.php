<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'category' => ['required', 'string', Rule::in(Service::getCategories())],
            'estimated_time' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The service name is required.',
            'name.min' => 'The service name must be at least 3 characters.',
            'name.max' => 'The service name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'base_price.required' => 'The base price is required.',
            'base_price.numeric' => 'The base price must be a valid number.',
            'base_price.min' => 'The base price must be at least 0.',
            'base_price.max' => 'The base price cannot exceed 999,999.99.',
            'category.required' => 'The category is required.',
            'category.in' => 'The selected category is invalid.',
            'estimated_time.integer' => 'The estimated time must be a valid number.',
            'estimated_time.min' => 'The estimated time must be at least 1 minute.',
            'estimated_time.max' => 'The estimated time cannot exceed 9999 minutes.',
            'is_active.boolean' => 'The is_active field must be true or false.',
        ];
    }
}
