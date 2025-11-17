<?php

declare(strict_types=1);

namespace AppOficina\Vehicles\Entities;

use AppOficina\Vehicles\Enums\TransmissionType;
use AppOficina\Vehicles\Enums\VehicleType;
use AppOficina\Vehicles\Exceptions\InvalidVehicleException;
use AppOficina\Vehicles\ValueObjects\LicensePlate;
use AppOficina\Vehicles\ValueObjects\Vin;
use AppOficina\Shared\Entity\Entity;
use DateTimeImmutable;
use Symfony\Component\Uid\Ulid;

class Vehicle extends Entity
{
    private function __construct(
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly VehicleType $type,
        public readonly Ulid $clientId,
        ?Ulid $id = null,
        public readonly string $color = '',
        public readonly int $cilinderCapacity = 0,
        public readonly int $mileage = 0,
        public readonly string $observations = '',
        public readonly ?LicensePlate $licensePlate = null,
        public readonly ?Vin $vin = null,
        public readonly ?TransmissionType $transmission = null,
    ) {
        $this->id = $id ?? new Ulid();
        $this->validate();
    }

    public static function create(
        string $brand,
        string $model,
        int $year,
        string $vehicleType,
        Ulid $clientId,
        ?Ulid $id = null
    ): self {
        return new self(
            brand: $brand,
            model: $model,
            year: $year,
            type: VehicleType::from($vehicleType),
            clientId: $clientId,
            id: $id,
        );
    }

    private function validate(): void
    {
        $this->validateBrand();
        $this->validateModel();
        $this->validateYear();
    }

    public function withLicensePlate(string $plate): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: new LicensePlate($plate),
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withVin(string $vin): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: new Vin($vin),
            transmission: $this->transmission,
        );
    }

    public function withTransmission(string $transmission): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: TransmissionType::from($transmission),
        );
    }

    public function withColor(string $color): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withCilinderCapacity(int $cilinderCapacity): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withMileage(int $mileage): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withObservations(string $observations): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withBrand(string $brand): self
    {
        return new self(
            brand: $brand,
            model: $this->model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withModel(string $model): self
    {
        return new self(
            brand: $this->brand,
            model: $model,
            year: $this->year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withYear(int $year): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $year,
            type: $this->type,
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withType(string $vehicleType): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: VehicleType::from($vehicleType),
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licensePlate: $this->licensePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function licensePlate(): string
    {
        return $this->licensePlate?->value() ?? '';
    }

    public function vin(): string
    {
        return $this->vin?->value() ?? '';
    }

    public function transmission(): string
    {
        return $this->transmission?->value ?? '';
    }

    public function type(): string
    {
        return $this->type->value;
    }

    private function validateBrand(): void
    {
        if ($this->brand === '') {
            throw new InvalidVehicleException('Brand is required');
        }

        if (strlen($this->brand) < 3) {
            throw new InvalidVehicleException('Brand must be at least 3 characters long');
        }
    }

    private function validateModel(): void
    {
        if ($this->model === '') {
            throw new InvalidVehicleException('Model is required');
        }

        if (strlen($this->model) < 1) {
            throw new InvalidVehicleException('Model must be at least 1 character long');
        }
    }

    private function validateYear(): void
    {
        $currentDate = new DateTimeImmutable();
        $startYear = 1886; // Year of the first car invention
        $currentYear = (int)$currentDate->format('Y');

        if ($this->year < $startYear || $this->year > $currentYear) {
            throw new InvalidVehicleException("Year must be between {$startYear} and {$currentYear}");
        }
    }
}
