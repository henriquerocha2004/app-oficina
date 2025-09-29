<?php

declare(strict_types=1);

namespace Tests\Fakes;

use AppOficina\Clients\Entities\Client;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Shared\Search\SearchRequest;
use AppOficina\Shared\Search\SearchResponse;
use AppOficina\Shared\Entity\Entity;
use Symfony\Component\Uid\Ulid;

final class InMemoryClientRepository implements ClientRepositoryInterface
{
    /** @var array<string, Client> */
    private array $store = [];

    public function save(Entity $entity): void
    {
        /** @var Client $entity */
        $this->store[(string) $entity->id] = $entity;
    }

    public function update(Entity $entity): void
    {
        /** @var Client $entity */
        $this->store[(string) $entity->id] = $entity;
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

        // columnSearch filters (exact match)
        foreach ($request->columnSearch as $field => $value) {
            $items = array_filter($items, function ($c) use ($field, $value) {
                if (in_array($field, ['street', 'city', 'state', 'zip_code'])) {
                    if ($c->address === null) return false;
                    $prop = $this->addressFieldToProp($field);
                    return (string) $c->address->{$prop} === (string) $value;
                }

                if ($field === 'document' || $field === 'document_number') {
                    return (string) $c->document === preg_replace('/\D/', '', (string) $value);
                }

                return isset($c->{$field}) && (string) $c->{$field} === (string) $value;
            });
        }

        // search (term in name, email or document)
        if (trim($request->search) !== '') {
            $term = strtolower($request->search);
            $items = array_filter($items, function ($c) use ($term) {
                return str_contains(strtolower($c->name), $term)
                    || str_contains(strtolower($c->email), $term)
                    || str_contains((string) $c->document, $term);
            });
        }

        // sorting
        usort($items, function ($a, $b) use ($request) {
            $field = $request->sortField;

            $va = null;
            $vb = null;

            if (in_array($field, ['street', 'city', 'state', 'zip_code'])) {
                $pa = $a->address?->{$this->addressFieldToProp($field)} ?? null;
                $pb = $b->address?->{$this->addressFieldToProp($field)} ?? null;
                $va = $pa;
                $vb = $pb;
            } elseif ($field === 'document' || $field === 'document_number') {
                $va = (string) $a->document;
                $vb = (string) $b->document;
            } else {
                $va = $a->{$field} ?? null;
                $vb = $b->{$field} ?? null;
            }

            if ($va == $vb) return 0;

            $cmp = $va < $vb ? -1 : 1;
            return $request->sort === 'asc' ? $cmp : -$cmp;
        });

        $total = count($items);

        // pagination
        $limit = $request->limit;
        $offset = $request->getOffset();
        if ($limit > 0) {
            $items = array_slice($items, $offset, $limit);
        }

        return new SearchResponse($total, array_values($items));
    }

    public function all(): array
    {
        return array_values($this->store);
    }

    public function findByDocument(string $document): ?Client
    {
        foreach ($this->store as $client) {
            if ((string) $client->document === preg_replace('/\D/', '', $document)) {
                return $client;
            }
        }

        return null;
    }

    private function mapFieldToProperty(string $field): string
    {
        // map snake_case to domain property names when needed
        $map = [
            'id' => 'id',
            'name' => 'name',
            'email' => 'email',
            'document' => 'document',
            'document_number' => 'document',
            'street' => 'address->street',
            'city' => 'address->city',
            'state' => 'address->state',
            'zip_code' => 'address->zipCode',
            'phone' => 'phone',
        ];

        return $map[$field] ?? $field;
    }

    private function addressFieldToProp(string $field): string
    {
        return match ($field) {
            'street' => 'street',
            'city' => 'city',
            'state' => 'state',
            'zip_code' => 'zipCode',
            default => $field,
        };
    }
}
