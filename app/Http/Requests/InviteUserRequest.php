<?php

namespace App\Http\Requests;

use App\DTOs\InvitationInputDTO;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                // Email must not already be a user
                Rule::unique('users', 'email'),
                // Email must not have a pending invitation
                function ($attribute, $value, $fail) {
                    $existingInvitation = UserInvitation::where('email', $value)
                        ->pending()
                        ->where('expires_at', '>', now())
                        ->first();
                    if ($existingInvitation) {
                        $fail('Este email já possui um convite pendente.');
                    }
                },
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Forneça um email válido.',
            'email.unique' => 'Este email já está cadastrado como usuário.',
            'role_id.required' => 'O perfil é obrigatório.',
            'role_id.exists' => 'Perfil inválido.',
        ];
    }

    /**
     * Convert to DTO.
     */
    public function toDTO(): InvitationInputDTO
    {
        return InvitationInputDTO::fromArray([
            'email' => $this->input('email'),
            'role_id' => $this->input('role_id'),
            'invited_by_user_id' => auth()->id(),
        ]);
    }
}
