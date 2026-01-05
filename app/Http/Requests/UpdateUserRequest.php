<?php

namespace App\Http\Requests;

use App\DTOs\UserInputDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Forneça um email válido.',
            'email.unique' => 'Este email já está em uso.',
        ];
    }

    public function toDTO(): UserInputDTO
    {
        return UserInputDTO::fromArray([
            'name' => $this->input('name'),
            'email' => $this->input('email'),
        ]);
    }
}
