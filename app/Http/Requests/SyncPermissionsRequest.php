<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permission_ids' => ['present', 'array'],
            'permission_ids.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'permission_ids.present' => 'O campo permission_ids é obrigatório.',
            'permission_ids.array' => 'Formato inválido.',
            'permission_ids.*.exists' => 'Uma ou mais permissões são inválidas.',
        ];
    }
}
