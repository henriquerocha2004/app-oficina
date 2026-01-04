<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'manufacturer' => $this->manufacturer,
            'category' => $this->category,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_level' => $this->min_stock_level,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price,
            'suggested_price' => $this->suggested_price,
            'is_active' => $this->is_active,
            'is_low_stock' => $this->is_low_stock,
            'suppliers' => $this->whenLoaded('suppliers', function () {
                return $this->suppliers->map(function ($supplier) {
                    return [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'supplier_sku' => $supplier->pivot->supplier_sku,
                        'cost_price' => $supplier->pivot->cost_price,
                        'lead_time_days' => $supplier->pivot->lead_time_days,
                        'min_order_quantity' => $supplier->pivot->min_order_quantity,
                        'is_preferred' => $supplier->pivot->is_preferred,
                        'notes' => $supplier->pivot->notes,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
