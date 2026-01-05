<?php

namespace App\Http\Requests;

use App\DTOs\AcceptInvitationDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AcceptInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token inválido.',
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        ];
    }

    /**
     * Convert to DTO.
     */
    public function toDTO(): AcceptInvitationDTO
    {
        return AcceptInvitationDTO::fromArray([
            'token' => $this->input('token'),
            'name' => $this->input('name'),
            'password' => $this->input('password'),
        ]);
    }
}
