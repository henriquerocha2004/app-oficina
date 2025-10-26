<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCarRequest extends FormRequest
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
            'year' => ['required', 'integer', 'min:1886', 'max:' . date('Y')],
            'typeCar' => ['required', 'string', 'in:sedan,hatchback,suv,coupe,convertible,wagon,van,pickup'],
            'clientId' => ['required', 'string', 'max:26'],
            'licencePlate' => ['nullable', 'string', 'regex:/^[A-Za-z]{3}-?\d{4}$|^[A-Za-z]{3}\d[A-Za-z]\d{2}$/'],
            'vin' => ['nullable', 'string', 'regex:/^[A-HJ-NPR-Z0-9]{17}$/'],
            'color' => ['nullable', 'string', 'max:50'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', 'string', 'in:manual,automatic'],
            'engineSize' => ['nullable', 'numeric', 'min:0'],
            'fuelType' => ['nullable', 'string', 'max:50'],
            'observations' => ['nullable', 'string'],
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
            'typeCar.required' => 'The car type field is required.',
            'typeCar.in' => 'The car type must be one of: sedan, hatchback, suv, coupe, ' .
                'convertible, wagon, van, pickup.',
            'clientId.required' => 'The client ID field is required.',
            'licencePlate.regex' => 'The licence plate format is invalid.',
            'vin.regex' => 'The VIN format is invalid.',
            'transmission.in' => 'The transmission must be manual or automatic.',
        ];
    }
}
