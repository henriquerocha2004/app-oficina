<?php

namespace AppOficina\Infra\Repository\Vehicles;

use App\Models\VehicleModel;
use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Infra\Repository\BaseEloquentRepository;
use AppOficina\Shared\Mapper\MapperInterface;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Uid\Ulid;

class VehicleEloquentRepository extends BaseEloquentRepository implements VehicleRepositoryInterface
{
    public function getModel(): Model
    {
        return app(VehicleModel::class);
    }

    public function getMapper(): MapperInterface
    {
        return app(VehicleEloquentMapper::class);
    }

    public function getColumnsSearchByTerm(): array
    {
        return ['brand', 'model', 'license_plate', 'vin'];
    }

    public function findByLicensePlate(string $licensePlate): ?Vehicle
    {
        $model = $this->getModel()->where('license_plate', $licensePlate)->first();
        if (!$model) {
            return null;
        }
        return $this->getMapper()->toDomain($model);
    }

    public function findByVin(string $vin): ?Vehicle
    {
        $model = $this->getModel()->where('vin', $vin)->first();
        if (!$model) {
            return null;
        }
        return $this->getMapper()->toDomain($model);
    }

    /**
     * @return array<Vehicle>
     */
    public function findByClientId(Ulid $clientId): array
    {
        $models = $this->getModel()->where('client_id', $clientId->toString())->get();

        return $models->map(fn($model) => $this->getMapper()->toDomain($model))->toArray();
    }
}
