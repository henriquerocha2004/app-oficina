<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => ['sometimes', 'string', 'min:3', 'max:255'],
            'model' => ['sometimes', 'string', 'min:1', 'max:255'],
            'year' => ['sometimes', 'integer', 'min:1886', 'max:' . date('Y') + 1],
            'color' => ['sometimes', 'nullable', 'string', 'max:50'],
            'client_id' => ['sometimes', 'string', 'max:26'],
            'plate' => ['sometimes', 'string', 'regex:/^[A-Za-z]{3}-?\d{4}$|^[A-Za-z]{3}\d[A-Za-z]\d{2}$/'],
            'vehicle_type' => ['sometimes', 'nullable', 'string', 'in:car,motorcycle'],
            'cilinder_capacity' => ['sometimes', 'nullable', 'string', 'max:50'],
            'fuel' => ['sometimes', 'nullable', 'string', 'in:alcohol,gasoline,diesel'],
            'transmission' => ['sometimes', 'nullable', 'string', 'in:manual,automatic'],
            'mileage' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'vin' => ['sometimes', 'nullable', 'string', 'max:17'],
            'observations' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand.min' => 'The brand must be at least 3 characters.',
            'model.min' => 'The model must be at least 1 character.',
            'year.min' => 'The year must be at least 1886.',
            'year.max' => 'The year cannot be in the future.',
            'plate.regex' => 'The plate format is invalid.',
        ];
    }
}
