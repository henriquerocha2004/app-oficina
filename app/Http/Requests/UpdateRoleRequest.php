<?php

namespace App\Http\Requests;

use App\DTOs\RoleInputDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug,' . $roleId, 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique' => 'Este slug já está em uso.',
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífens.',
        ];
    }

    public function toDTO(): RoleInputDTO
    {
        return RoleInputDTO::fromArray([
            'name' => $this->input('name'),
            'slug' => $this->input('slug'),
            'description' => $this->input('description'),
        ]);
    }
}
