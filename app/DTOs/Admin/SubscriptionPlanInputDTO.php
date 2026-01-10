<?php

namespace App\DTOs\Admin;

readonly class SubscriptionPlanInputDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public float $price,
        public string $billingCycle,
        public int $maxUsers,
        public int $maxClients,
        public int $maxVehicles,
        public int $maxServicesPerMonth,
        public ?array $features,
        public bool $isActive = true,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            billingCycle: $data['billing_cycle'],
            maxUsers: (int) $data['max_users'],
            maxClients: (int) $data['max_clients'],
            maxVehicles: (int) $data['max_vehicles'],
            maxServicesPerMonth: (int) $data['max_services_per_month'],
            features: isset($data['features']) ? array_map('trim', explode(',', $data['features'])) : null,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        // Map price and billing_cycle to the correct database columns
        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];

        // Set price based on billing cycle
        if ($this->billingCycle === 'monthly') {
            $data['price_monthly'] = $this->price;
            $data['price_yearly'] = $this->price * 10; // Aproximadamente 2 meses grátis
        } else {
            $data['price_yearly'] = $this->price;
            $data['price_monthly'] = round($this->price / 10, 2);
        }

        // Map limits
        $data['limits'] = [
            'max_users' => $this->maxUsers,
            'max_clients' => $this->maxClients,
            'max_vehicles' => $this->maxVehicles,
            'max_services_per_month' => $this->maxServicesPerMonth,
        ];

        // Map features
        $data['features'] = $this->features;

        return $data;
    }
}
