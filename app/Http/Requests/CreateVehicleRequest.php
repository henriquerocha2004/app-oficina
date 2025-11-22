<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'min:3', 'max:255'],
            'model' => ['required', 'string', 'min:1', 'max:255'],
            'year' => ['required', 'integer', 'min:1886', 'max:' . date('Y') + 1],
            'color' => ['nullable', 'string', 'max:50'],
            'client_id' => ['required', 'string', 'max:26'],
            'plate' => ['required', 'string', 'regex:/^[A-Z]{3}-?[0-9]{1}[A-Z0-9][0-9]{2}$/'],
            'vehicle_type' => ['nullable', 'string', 'in:car,motorcycle'],
            'cilinder_capacity' => ['nullable', 'string', 'max:50'],
            'fuel' => ['nullable', 'string', 'in:alcohol,gasoline,diesel'],
            'transmission' => ['nullable', 'string', 'in:manual,automatic'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'vin' => ['nullable', 'string', 'max:17'],
            'observations' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand.required' => 'The brand field is required.',
            'brand.min' => 'The brand must be at least 3 characters.',
            'model.required' => 'The model field is required.',
            'year.required' => 'The year field is required.',
            'year.min' => 'The year must be at least 1886.',
            'year.max' => 'The year cannot be in the future.',
            'color.required' => 'The color field is required.',
            'client_id.required' => 'The client ID field is required.',
            'plate.required' => 'The plate field is required.',
            'plate.regex' => 'The plate format is invalid.',
        ];
    }
}
