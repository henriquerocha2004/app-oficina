<?php

namespace App\DTOs\Admin;

readonly class TenantUpdateDTO
{
    public function __construct(
        public string $name,
        public string $subscriptionPlanId,
        public bool $isActive,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            subscriptionPlanId: $data['subscription_plan_id'],
            isActive: $data['is_active'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'subscription_plan_id' => $this->subscriptionPlanId,
            'is_active' => $this->isActive,
        ];
    }
}
