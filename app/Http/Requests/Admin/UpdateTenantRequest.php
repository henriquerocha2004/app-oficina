<?php

namespace App\Http\Requests\Admin;

use App\DTOs\Admin\TenantUpdateDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);

            return;
        }

        $this->merge([
            'is_active' => false,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da oficina é obrigatório.',
            'name.max' => 'O nome da oficina não pode ter mais de 255 caracteres.',
            'subscription_plan_id.required' => 'O plano de assinatura é obrigatório.',
            'subscription_plan_id.exists' => 'O plano de assinatura selecionado não existe.',
        ];
    }

    public function toDTO(): TenantUpdateDTO
    {
        return TenantUpdateDTO::fromRequest($this->validated());
    }
}
