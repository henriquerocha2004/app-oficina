<?php

namespace App\Http\Controllers;

use App\DTOs\ClientInputDTO;
use App\DTOs\SearchDTO;
use App\Services\ClientService;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService
    ) {
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('clients/Index');
    }

    public function store(ClientRequest $request): JsonResponse
    {
        try {
            $dto = ClientInputDTO::fromArray($request->validated());
            $result = $this->clientService->create($dto);

            if (!$result->created) {
                return response()->json([
                    'message' => 'Client already exists',
                    'client_id' => $result->client->id,
                ], Response::HTTP_OK);
            }

            return response()->json([
                'message' => 'Client created successfully',
                'client_id' => $result->client->id,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating client', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the client',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showById(string $id): JsonResponse
    {
        try {
            $client = $this->clientService->find($id);

            if (!$client) {
                return response()->json(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'client' => new ClientResource($client),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching client', [
                'client_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching the client',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(string $id, ClientRequest $request): JsonResponse
    {
        try {
            $dto = ClientInputDTO::fromArray($request->validated());
            $this->clientService->update($id, $dto);

            return response()->json([
                'message' => 'Client updated successfully',
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Client not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error updating client', [
                'client_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the client',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $deleted = $this->clientService->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Client deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting client', [
                'client_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while deleting the client',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $clients = $this->clientService->list($searchDTO);

            return response()->json([
                'clients' => [
                    'items' => ClientResource::collection($clients->items()),
                    'total_items' => $clients->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error listing clients', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while listing clients',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
