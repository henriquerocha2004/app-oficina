<?php

namespace App\Http\Requests\Admin;

use App\DTOs\Admin\AdminLoginDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
            ],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Por favor, forneça um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.uncompromised' => 'Esta senha foi encontrada em vazamentos de dados. Por favor, escolha outra.',
        ];
    }

    /**
     * Create a DTO from validated request data.
     */
    public function toDTO(): AdminLoginDTO
    {
        return AdminLoginDTO::fromRequest($this->validated());
    }
}
