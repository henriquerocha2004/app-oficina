<?php

namespace App\Http\Requests\Admin;

use App\DTOs\Admin\SubscriptionPlanInputDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subscription_plans', 'slug')->ignore($planId),
                'alpha_dash',
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'max_users' => ['required', 'integer', 'min:1'],
            'max_clients' => ['required', 'integer', 'min:0'],
            'max_vehicles' => ['required', 'integer', 'min:0'],
            'max_services_per_month' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do plano é obrigatório.',
            'name.max' => 'O nome do plano não pode ter mais de 255 caracteres.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique' => 'Este slug já está em uso.',
            'slug.alpha_dash' => 'O slug pode conter apenas letras, números, traços e underscores.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço deve ser maior ou igual a zero.',
            'billing_cycle.required' => 'O ciclo de cobrança é obrigatório.',
            'billing_cycle.in' => 'O ciclo de cobrança deve ser mensal ou anual.',
            'max_users.required' => 'O número máximo de usuários é obrigatório.',
            'max_users.integer' => 'O número máximo de usuários deve ser um número inteiro.',
            'max_users.min' => 'O número máximo de usuários deve ser pelo menos 1.',
        ];
    }

    public function toDTO(): SubscriptionPlanInputDTO
    {
        return SubscriptionPlanInputDTO::fromRequest($this->validated());
    }
}
