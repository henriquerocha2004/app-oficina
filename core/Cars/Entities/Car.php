<?php

declare(strict_types=1);

namespace AppOficina\Cars\Entities;

use AppOficina\Cars\Enums\TransmissionType;
use AppOficina\Cars\Enums\TypeCarEnum;
use AppOficina\Cars\Exceptions\InvalidCarException;
use AppOficina\Cars\ValueObjects\LicencePlate;
use AppOficina\Cars\ValueObjects\Vin;
use AppOficina\Shared\Entity\Entity;
use DateTimeImmutable;
use Symfony\Component\Uid\Ulid;

class Car extends Entity
{
    private function __construct(
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly TypeCarEnum $type,
        public readonly Ulid $clientId,
        ?Ulid $id = null,
        public readonly string $color = '',
        public readonly int $cilinderCapacity = 0,
        public readonly int $mileage = 0,
        public readonly string $observations = '',
        public readonly ?LicencePlate $licencePlate = null,
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
        string $typeCar,
        Ulid $clientId,
        ?Ulid $id = null
    ): self {
        return new self(
            brand: $brand,
            model: $model,
            year: $year,
            type: TypeCarEnum::from($typeCar),
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

    public function withLicencePlate(string $plate): self
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
            licencePlate: new LicencePlate($plate),
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
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
            licencePlate: $this->licencePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function withType(string $typeCar): self
    {
        return new self(
            brand: $this->brand,
            model: $this->model,
            year: $this->year,
            type: TypeCarEnum::from($typeCar),
            clientId: $this->clientId,
            id: $this->id,
            color: $this->color,
            cilinderCapacity: $this->cilinderCapacity,
            mileage: $this->mileage,
            observations: $this->observations,
            licencePlate: $this->licencePlate,
            vin: $this->vin,
            transmission: $this->transmission,
        );
    }

    public function licencePlate(): string
    {
        return $this->licencePlate?->value() ?? '';
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
            throw new InvalidCarException('Brand is required');
        }

        if (strlen($this->brand) < 3) {
            throw new InvalidCarException('Brand must be at least 3 characters long');
        }
    }

    private function validateModel(): void
    {
        if ($this->model === '') {
            throw new InvalidCarException('Model is required');
        }

        if (strlen($this->model) < 1) {
            throw new InvalidCarException('Model must be at least 1 character long');
        }
    }

    private function validateYear(): void
    {
        $currentDate = new DateTimeImmutable();
        $startYear = 1886; // Year of the first car invention
        $currentYear = (int)$currentDate->format('Y');

        if ($this->year < $startYear || $this->year > $currentYear) {
            throw new InvalidCarException("Year must be between {$startYear} and {$currentYear}");
        }
    }
}
