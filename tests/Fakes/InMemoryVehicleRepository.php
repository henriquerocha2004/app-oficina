<?php

declare(strict_types=1);

namespace Tests\Fakes;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;
use AppOficina\Shared\Entity\Entity;
use Symfony\Component\Uid\Ulid;

final class InMemoryVehicleRepository implements VehicleRepositoryInterface
{
    /** @var array<string, Vehicle> */
    private array $store = [];

    /** @var array<string, array<string>> */
    private array $clientVehicles = [];

    public function save(Entity $entity): void
    {
        /** @var Vehicle $entity */
        $this->store[(string) $entity->id] = $entity;
        $this->addVehicleToClient((string) $entity->id, (string) $entity->clientId);
    }

    public function update(Entity $entity): void
    {
        /** @var Vehicle $entity */
        $this->store[(string) $entity->id] = $entity;
        $this->addVehicleToClient((string) $entity->id, (string) $entity->clientId);
    }

    public function delete(Ulid $id): void
    {
        unset($this->store[(string) $id]);
    }

    public function findById(Ulid $id): ?Entity
    {
        return $this->store[(string) $id] ?? null;
    }

    public function findAll(SearchRequest $request): SearchResponse
    {
        $items = array_values($this->store);
        $total = count($items);

        // Apply pagination
        $start = ($request->page - 1) * $request->limit;
        $items = array_slice($items, $start, $request->limit);

        return new SearchResponse(
            totalItems: $total,
            items: array_map(fn($car) => $car->toArray(), $items),
        );
    }

    public function findByLicencePlate(string $licencePlate): ?Vehicle
    {
        // Normalize the input licence plate format
        $normalizedInput = strtoupper(str_replace(['-', ' '], '', $licencePlate));

        foreach ($this->store as $car) {
            $carPlate = $car->licencePlate();
            $normalizedVehiclePlate = strtoupper(str_replace(['-', ' '], '', $carPlate));
            if ($normalizedVehiclePlate === $normalizedInput) {
                return $car;
            }
        }
        return null;
    }

    public function findByVin(string $vin): ?Vehicle
    {
        foreach ($this->store as $car) {
            if ($car->vin() === $vin) {
                return $car;
            }
        }
        return null;
    }

    /**
     * @return array<Vehicle>
     */
    public function findByClientId(Ulid $clientId): array
    {
        return array_values(array_filter(
            $this->store,
            fn(Vehicle $car) => $car->clientId->equals($clientId)
        ));
    }

    /**
     * @return array<Vehicle>
     */
    public function all(): array
    {
        return array_values($this->store);
    }

    public function addVehicleToClient(string $carId, string $clientId): void
    {
        if (!isset($this->clientVehicles[$clientId])) {
            $this->clientVehicles[$clientId] = [];
        }
        $this->clientVehicles[$clientId][] = $carId;
    }
}
