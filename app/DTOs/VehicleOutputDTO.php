<?php

namespace App\DTOs;

use App\Models\Vehicle;

readonly class VehicleOutputDTO
{
    public function __construct(
        public string $id,
        public ?string $plate = null,
        public ?string $brand = null,
        public ?string $model = null,
        public ?int $year = null,
        public ?string $color = null,
        public ?string $client_id = null,
        public ?string $vehicle_type = null,
        public ?string $cilinder_capacity = null,
        public ?string $fuel = null,
        public ?string $transmission = null,
        public ?int $mileage = null,
        public ?string $vin = null,
        public ?string $observations = null,
        public ?ClientOutputDTO $client = null,
    ) {
    }

    public static function fromModel(Vehicle $vehicle): self
    {
        return new self(
            id: $vehicle->id,
            plate: $vehicle->license_plate,
            brand: $vehicle->brand,
            model: $vehicle->model,
            year: $vehicle->year,
            color: $vehicle->color,
            client_id: $vehicle->client_id,
            vehicle_type: $vehicle->vehicle_type,
            cilinder_capacity: $vehicle->cilinder_capacity ? (string) $vehicle->cilinder_capacity : null,
            fuel: $vehicle->fuel,
            transmission: $vehicle->transmission,
            mileage: $vehicle->mileage,
            vin: $vehicle->vin,
            observations: $vehicle->observations,
            client: $vehicle->relationLoaded('client') && $vehicle->client
                ? ClientOutputDTO::fromModel($vehicle->client)
                : null,
        );
    }
}
