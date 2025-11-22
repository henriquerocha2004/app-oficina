<?php

namespace App\DTOs;

readonly class VehicleInputDTO
{
    public function __construct(
        public string $plate,
        public string $brand,
        public string $model,
        public int $year,
        public string $color,
        public ?string $client_id = null,
        public ?string $vehicle_type = null,
        public ?string $cilinder_capacity = null,
        public ?string $fuel = null,
        public ?string $transmission = null,
        public ?int $mileage = null,
        public ?string $vin = null,
        public ?string $observations = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            plate: $data['plate'],
            brand: $data['brand'],
            model: $data['model'],
            year: (int) $data['year'],
            color: $data['color'] ?? '',
            client_id: $data['client_id'] ?? null,
            vehicle_type: $data['vehicle_type'] ?? null,
            cilinder_capacity: $data['cilinder_capacity'] ?? null,
            fuel: $data['fuel'] ?? null,
            transmission: $data['transmission'] ?? null,
            mileage: isset($data['mileage']) && $data['mileage'] !== '' ? (int) $data['mileage'] : null,
            vin: $data['vin'] ?? null,
            observations: $data['observations'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'license_plate' => $this->plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'color' => $this->color,
            'client_id' => $this->client_id,
            'vehicle_type' => $this->vehicle_type,
            'cilinder_capacity' => $this->cilinder_capacity,
            'fuel' => $this->fuel,
            'transmission' => $this->transmission,
            'mileage' => $this->mileage,
            'vin' => $this->vin,
            'observations' => $this->observations,
        ], fn($value) => !is_null($value) && $value !== '');
    }
}
