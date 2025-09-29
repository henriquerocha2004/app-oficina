<?php

namespace App\Http\Controllers;

use Throwable;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\SearchInputRequest;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;
use AppOficina\Clients\UseCases\FindClientByIdUseCase;
use AppOficina\Clients\Exceptions\ClientNotFoundException;
use AppOficina\Clients\UseCases\CreateClient\CreateClientUseCase;
use AppOficina\Clients\UseCases\UpdateClient\UpdateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\Input as CreateClientInput;
use AppOficina\Clients\UseCases\DeleteClientUseCase;
use AppOficina\Clients\UseCases\ListClientsUseCase;
use AppOficina\Clients\UseCases\UpdateClient\Input as UpdateClientInput;
use AppOficina\Shared\Search\SearchRequest;
use Request;

class ClientController extends Controller
{
    public function __construct(
       private readonly CreateClientUseCase $createClientUseCase,
       private readonly FindClientByIdUseCase $findClientByIdUseCase,
       private readonly UpdateClientUseCase $updateClientUseCase,
       private readonly DeleteClientUseCase $deleteClientUseCase,
       private readonly ListClientsUseCase $findClientsByFiltersUseCase
    ){
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('clients/Index');
    }

    public function store(ClientRequest $request): JsonResponse
    {
        try {
            $input = new CreateClientInput(
                name: $request->validated('name'),
                email: $request->validated('email'),
                document: $request->validated('document'),
                address: $request->validated('address'),
                phone: $request->validated('phone'),
                observations: $request->validated('observations'),
            );

            $output = $this->createClientUseCase->execute($input);

            return response()->json([
                'message' => 'Client created successfully',
                'client_id' => $output->clientId,
            ], Response::HTTP_CREATED);
        } catch (Throwable $throwable) {

            Log::error('Error creating client: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Error creating client',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showById(string $id): JsonResponse
    {
        try {
            $client = $this->findClientByIdUseCase->execute($id);

            return response()->json([
                'client' => $client,
            ], Response::HTTP_OK);
        } catch (ClientNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error fetching client: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'client_id' => $id,
            ]);

            return response()->json([
                'message' => 'Error fetching client',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(string $id, ClientRequest $request): JsonResponse
    {
        try {
            $input = new UpdateClientInput(
                clientId: $id,
                name: $request->validated('name'),
                email: $request->validated('email'),
                document: $request->validated('document'),
                address: $request->validated('address'),
                phone: $request->validated('phone'),
                observations: $request->validated('observations'),
            );

            $this->updateClientUseCase->execute($input);

            return response()->json([
                'message' => 'Client updated successfully',
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error('Error updating client: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'client_id' => $id,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Error updating client',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->deleteClientUseCase->execute($id);

            return response()->json([
                'message' => 'Client deleted successfully',
            ], Response::HTTP_OK);
        } catch (ClientNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error deleting client: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'client_id' => $id,
            ]);

            return response()->json([
                'message' => 'Error deleting client',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByFilters(SearchInputRequest $request): JsonResponse
    {
        try {
            $filters = new SearchRequest(
                page: (int) $request->validated('page', 1),
                limit: (int) $request->validated('limit', 10),
                sort: $request->validated('sort', 'asc'),
                sortField: $request->validated('sortField', 'id'),
                search: $request->validated('search', ''),
                columnSearch: $request->validated('columnSearch', []),
            );

            $clients = $this->findClientsByFiltersUseCase->execute($filters);

            return response()->json([
                'clients' => $clients,
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error('Error fetching clients by filters: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'filters' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Error fetching clients by filters',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
