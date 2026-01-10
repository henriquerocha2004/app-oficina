<?php

namespace App\Services;

use App\DTOs\ClientInputDTO;
use App\DTOs\ClientOutputDTO;
use App\DTOs\CreateClientResultDTO;
use App\DTOs\SearchDTO;
use App\Models\Client;
use App\Services\Traits\QueryBuilderTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientService
{
    use QueryBuilderTrait;

    public function create(ClientInputDTO $dto): CreateClientResultDTO
    {
        $existingClient = Client::where('document_number', $dto->document_number)->first();

        if ($existingClient) {
            return new CreateClientResultDTO(
                client: ClientOutputDTO::fromModel($existingClient),
                created: false
            );
        }

        $client = Client::create($dto->toArray());

        return new CreateClientResultDTO(
            client: ClientOutputDTO::fromModel($client),
            created: true
        );
    }

    public function update(string $id, ClientInputDTO $dto): void
    {
        $client = Client::findOrFail($id);
        $client->update($dto->toArray());
    }

    public function delete(string $id): bool
    {
        $client = Client::find($id);

        if (!$client) {
            return false;
        }

        return $client->delete();
    }

    public function find(string $id): ?ClientOutputDTO
    {
        $client = Client::find($id);

        return $client ? ClientOutputDTO::fromModel($client) : null;
    }

    public function list(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $query = Client::query();

        $searchableColumns = ['name', 'email', 'document_number', 'phone'];

        return $this->applySearchAndFilters(
            $query,
            $searchDTO,
            $searchableColumns
        );
    }
}
