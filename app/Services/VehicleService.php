<?php

namespace App\Services;

use App\DTOs\VehicleInputDTO;
use App\DTOs\VehicleOutputDTO;
use App\DTOs\SearchDTO;
use App\Models\Vehicle;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    use QueryBuilderTrait;

    public function create(VehicleInputDTO $dto): VehicleOutputDTO
    {
        $vehicle = Vehicle::create($dto->toArray());

        return VehicleOutputDTO::fromModel($vehicle);
    }

    public function update(string $id, VehicleInputDTO $dto): void
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($dto->toArray());
    }

    public function delete(string $id): bool
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return false;
        }

        return $vehicle->delete();
    }

    public function find(string $id): ?Vehicle
    {
        return Vehicle::with('client')->find($id);
    }

    public function findByLicensePlate(string $licensePlate): ?Vehicle
    {
        return Vehicle::with('client')->where('license_plate', $licensePlate)->first();
    }

    public function findByVin(string $vin): ?Vehicle
    {
        return Vehicle::with('client')->where('vin', $vin)->first();
    }

    public function findByClientId(string $clientId)
    {
        return Vehicle::with('client')->where('client_id', $clientId)->get();
    }

    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = Vehicle::with('client');

        // Busca por nome do cliente usando whereHas
        if (!empty($searchDTO->search)) {
            $search = $searchDTO->search;
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro por tipo de veículo
        if (!empty($searchDTO->filters['vehicle_type'])) {
            $query->where('vehicle_type', $searchDTO->filters['vehicle_type']);
        }

        // Aplicar ordenação
        if (!empty($searchDTO->sort_by)) {
            $query->orderBy(
                $searchDTO->sort_by,
                $searchDTO->sort_direction ?? 'asc'
            );
        }

        return $query->paginate($searchDTO->per_page ?? 10);
    }
}
