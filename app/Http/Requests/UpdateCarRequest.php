<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
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
            'year' => ['sometimes', 'integer', 'min:1886', 'max:' . date('Y')],
            'typeCar' => ['sometimes', 'string', 'in:sedan,hatchback,suv,coupe,convertible,wagon,van,pickup'],
            'clientId' => ['sometimes', 'string', 'max:26'],
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
            'brand.min' => 'The brand must be at least 3 characters.',
            'model.min' => 'The model must be at least 1 character.',
            'year.min' => 'The year must be at least 1886.',
            'year.max' => 'The year cannot be in the future.',
            'typeCar.in' => 'The car type must be one of: sedan, hatchback, suv, coupe, ' .
                'convertible, wagon, van, pickup.',
            'licencePlate.regex' => 'The licence plate format is invalid.',
            'vin.regex' => 'The VIN format is invalid.',
            'transmission.in' => 'The transmission must be manual or automatic.',
        ];
    }
}
