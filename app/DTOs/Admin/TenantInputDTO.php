<?php

namespace App\DTOs\Admin;

readonly class TenantInputDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $subscriptionPlanId,
        public string $domain,
        public string $adminName,
        public string $adminEmail,
        public string $adminPassword,
        public bool $isActive = true,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            subscriptionPlanId: $data['subscription_plan_id'],
            domain: $data['domain'],
            adminName: $data['admin_name'],
            adminEmail: $data['admin_email'],
            adminPassword: $data['admin_password'],
            isActive: $data['is_active'] ?? true,
        );
    }

    public function getTenantData(): array
    {
        return [
            'id' => $this->slug,
            'slug' => $this->slug,
            'name' => $this->name,
            'subscription_plan_id' => $this->subscriptionPlanId,
            'is_active' => $this->isActive,
        ];
    }

    public function getAdminData(): array
    {
        return [
            'name' => $this->adminName,
            'email' => $this->adminEmail,
            'password' => $this->adminPassword,
        ];
    }

    public function getDomainName(): string
    {
        $domainSuffix = config('app.env') === 'local' ? '.localhost' : '.' . config('app.domain');
        return $this->domain . $domainSuffix;
    }
}
