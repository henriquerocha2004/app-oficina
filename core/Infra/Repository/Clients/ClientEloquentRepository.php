<?php

namespace AppOficina\Infra\Repository\Clients;

use App\Models\ClientModel;
use AppOficina\Infra\Repository\BaseEloquentRepository;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Shared\Mapper\MapperInterface;
use Illuminate\Database\Eloquent\Model;

class ClientEloquentRepository extends BaseEloquentRepository implements ClientRepositoryInterface
{
    public function getModel(): Model
    {
        return app(ClientModel::class);
    }

    public function getMapper(): MapperInterface
    {
        return app(ClientEloquentMapper::class);
    }

    public function getColumnsSearchByTerm(): array
    {
        return ['name', 'email', 'document_number'];
    }

    public function findByDocument(string $document): ?\AppOficina\Clients\Entities\Client
    {
        $model = $this->getModel()->where('document_number', $document)->first();
        if (!$model) {
            return null;
        }
        return $this->getMapper()->toDomain($model);
    }
}
