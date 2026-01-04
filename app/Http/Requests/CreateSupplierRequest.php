<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document_number' => [
                'required',
                'string',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$|^\d{14}$/',
                'unique:suppliers,document_number'
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:10', 'max:20'],
            'mobile' => ['nullable', 'string', 'min:10', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'payment_term_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do fornecedor é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'document_number.required' => 'O CNPJ é obrigatório.',
            'document_number.regex' => 'CNPJ inválido. Use o formato: 00.000.000/0000-00',
            'document_number.unique' => 'Este CNPJ já está cadastrado.',
            'email.email' => 'Email inválido.',
            'phone.min' => 'O telefone deve ter pelo menos 10 caracteres.',
            'website.url' => 'URL inválida.',
            'state.size' => 'O estado deve ter 2 caracteres (ex: SP).',
        ];
    }
}
