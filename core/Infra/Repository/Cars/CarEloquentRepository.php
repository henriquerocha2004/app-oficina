<?php

namespace AppOficina\Infra\Repository\Cars;

use App\Models\CarsModel;
use AppOficina\Cars\Entities\Car;
use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Infra\Repository\BaseEloquentRepository;
use AppOficina\Shared\Mapper\MapperInterface;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Uid\Ulid;

class CarEloquentRepository extends BaseEloquentRepository implements CarRepositoryInterface
{
    public function getModel(): Model
    {
        return app(CarsModel::class);
    }

    public function getMapper(): MapperInterface
    {
        return app(CarEloquentMapper::class);
    }

    public function getColumnsSearchByTerm(): array
    {
        return ['brand', 'model', 'licence_plate', 'vin'];
    }

    public function findByLicencePlate(string $licencePlate): ?Car
    {
        $model = $this->getModel()->where('licence_plate', $licencePlate)->first();
        if (!$model) {
            return null;
        }
        return $this->getMapper()->toDomain($model);
    }

    public function findByVin(string $vin): ?Car
    {
        $model = $this->getModel()->where('vin', $vin)->first();
        if (!$model) {
            return null;
        }
        return $this->getMapper()->toDomain($model);
    }

    /**
     * @return array<Car>
     */
    public function findByClientId(Ulid $clientId): array
    {
        $models = $this->getModel()->where('client_id', $clientId->toString())->get();

        return $models->map(fn($model) => $this->getMapper()->toDomain($model))->toArray();
    }
}
