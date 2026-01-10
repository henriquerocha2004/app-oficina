<?php

namespace App\Http\Requests\Admin;

use App\DTOs\Admin\TenantInputDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tenants,slug', 'alpha_dash'],
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'domain' => ['required', 'string', 'max:255', 'alpha_dash'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da oficina é obrigatório.',
            'name.max' => 'O nome da oficina não pode ter mais de 255 caracteres.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique' => 'Este slug já está em uso.',
            'slug.alpha_dash' => 'O slug pode conter apenas letras, números, traços e underscores.',
            'subscription_plan_id.required' => 'O plano de assinatura é obrigatório.',
            'subscription_plan_id.exists' => 'O plano de assinatura selecionado não existe.',
            'domain.required' => 'O domínio é obrigatório.',
            'domain.alpha_dash' => 'O domínio pode conter apenas letras, números, traços e underscores.',
            'admin_name.required' => 'O nome do administrador é obrigatório.',
            'admin_email.required' => 'O email do administrador é obrigatório.',
            'admin_email.email' => 'O email do administrador deve ser um endereço válido.',
            'admin_password.required' => 'A senha do administrador é obrigatória.',
        ];
    }

    public function toDTO(): TenantInputDTO
    {
        return TenantInputDTO::fromRequest($this->validated());
    }
}
