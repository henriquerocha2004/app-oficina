<?php

namespace App\DTOs;

use App\Models\Service;

readonly class ServiceOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public float $base_price,
        public string $category,
        public ?string $description = null,
        public ?int $estimated_time = null,
        public bool $is_active = true,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    public static function fromModel(Service $service): self
    {
        return new self(
            id: $service->id,
            name: $service->name,
            base_price: (float) $service->base_price,
            category: $service->category,
            description: $service->description,
            estimated_time: $service->estimated_time,
            is_active: $service->is_active,
            created_at: $service->created_at?->toIso8601String(),
            updated_at: $service->updated_at?->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_price' => $this->base_price,
            'category' => $this->category,
            'description' => $this->description,
            'estimated_time' => $this->estimated_time,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
