<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'document' => ['required', 'string', 'max:20'],
            'address' => ['required', 'array'],
            'address.street' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:100'],
            'address.state' => ['required', 'string', 'max:100'],
            'address.zip_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:15'],                                
            'observations' => ['nullable', 'string'],
        ];
    }
            }
