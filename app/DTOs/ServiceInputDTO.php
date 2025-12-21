<?php

namespace App\DTOs;

readonly class ServiceInputDTO
{
    public function __construct(
        public string $name,
        public float $base_price,
        public string $category,
        public ?string $description = null,
        public ?int $estimated_time = null,
        public bool $is_active = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            base_price: (float) $data['base_price'],
            category: $data['category'],
            description: $data['description'] ?? null,
            estimated_time: isset($data['estimated_time']) && $data['estimated_time'] !== ''
                ? (int) $data['estimated_time']
                : null,
            is_active: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'base_price' => $this->base_price,
            'category' => $this->category,
            'description' => $this->description,
            'estimated_time' => $this->estimated_time,
            'is_active' => $this->is_active,
        ], fn($value) => $value !== null);
    }
}
