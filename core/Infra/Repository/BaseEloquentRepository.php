<?php

namespace AppOficina\Infra\Repository;

use AppOficina\Shared\Entity\Entity;
use AppOficina\Shared\Mapper\MapperInterface;
use AppOficina\Shared\Repository\RepositoryInterface;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Uid\Ulid;

abstract class BaseEloquentRepository implements RepositoryInterface
{
    abstract protected function getModel(): Model;
    abstract protected function getMapper(): MapperInterface;

    /** @return string[] */
    abstract protected function getColumnsSearchByTerm(): array;

    public function save(Entity $entity): void
    {
        $data = $this->getMapper()->toPersistence($entity);
        $this->getModel()->create((array) $data);
    }

    public function update(Entity $entity): void
    {
        $data = $this->getMapper()->toPersistence($entity);
        $model = $this->getModel()->find($entity->id);
        $model->update((array) $data);
    }

    public function delete(Ulid $id): void
    {
        $model = $this->getModel()->find($id->toString());
        $model->delete();
    }

    public function findById(Ulid $id): ?Entity
    {
        $model = $this->getModel()->find($id->toString());
        if (!$model) {
            return null;
        }
        return $this->getMapper()->toDomain($model);
    }

    public function findAll(SearchRequest $search): SearchResponse
    {
        $query = $this->getModel()->newQuery();

        if ($search->search) {
            $columns = $this->getColumnsSearchByTerm();
            $query->where(function (Builder $query) use ($columns, $search) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $search->search . '%');
                }
            });
        }

        if ($search->columnSearch) {
            foreach ($search->columnSearch as $column) {
                if (is_array($column) && isset($column['field']) && isset($column['value'])) {
                    $query->where($column['field'], '=', $column['value']);
                }
            }
        }

        $totalItems = $query->count();

        $query->orderBy($search->sortField, $search->sort);

        if ($search->limit > 0) {
            $query->limit($search->limit)->offset($search->getOffset());
        }

        $models = $query->get();
        $items = array_map(fn($model) => $this->getMapper()->toDomain($model)->toArray(), $models->all());

        return new SearchResponse(totalItems: $totalItems, items: $items);
    }
}
