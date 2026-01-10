<?php

namespace App\DTOs;

use App\Models\StockMovement;

readonly class StockMovementOutputDTO
{
    public function __construct(
        public string $id,
        public string $product_id,
        public string $movement_type,
        public int $quantity,
        public string $reason,
        public ?int $balance_after = null,
        public ?string $reference_type = null,
        public ?string $reference_id = null,
        public ?string $notes = null,
        public ?string $user_id = null,
        public ?string $product_name = null,
        public ?string $user_name = null,
        public ?string $created_at = null,
    ) {
    }

    public static function fromModel(StockMovement $movement): self
    {
        $productName = null;
        if ($movement->relationLoaded('product') && $movement->product) {
            $productName = $movement->product->name;
        }

        $userName = null;
        if ($movement->relationLoaded('user') && $movement->user) {
            $userName = $movement->user->name;
        }

        return new self(
            id: $movement->id,
            product_id: $movement->product_id,
            movement_type: $movement->movement_type,
            quantity: $movement->quantity,
            reason: $movement->reason,
            balance_after: $movement->balance_after,
            reference_type: $movement->reference_type,
            reference_id: $movement->reference_id,
            notes: $movement->notes,
            user_id: $movement->user_id,
            product_name: $productName,
            user_name: $userName,
            created_at: $movement->created_at?->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'movement_type' => $this->movement_type,
            'quantity' => $this->quantity,
            'balance_after' => $this->balance_after,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'created_at' => $this->created_at,
        ], fn($value) => $value !== null);
    }
}
