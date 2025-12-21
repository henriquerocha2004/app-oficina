<?php

namespace App\Http\Controllers;

use App\DTOs\ServiceInputDTO;
use App\DTOs\SearchDTO;
use App\Services\ServiceService;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class ServicesController extends Controller
{
    public function __construct(
        private ServiceService $serviceService
    ) {}

    public function index(): InertiaResponse
    {
        return Inertia::render('services/Index');
    }

    public function store(CreateServiceRequest $request): JsonResponse
    {
        try {
            $dto = ServiceInputDTO::fromArray($request->validated());
            $service = $this->serviceService->create($dto);

            return response()->json([
                'message' => 'Service created successfully',
                'service_id' => $service->id,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the service',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showById(string $id): JsonResponse
    {
        try {
            $service = $this->serviceService->find($id);

            if (!$service) {
                return response()->json(
                    ['message' => 'Service not found.'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json([
                'service' => new ServiceResource($service),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching service', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching the service',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(string $id, UpdateServiceRequest $request): JsonResponse
    {
        try {
            $dto = ServiceInputDTO::fromArray($request->validated());
            $this->serviceService->update($id, $dto);

            return response()->json([
                'message' => 'Service updated successfully',
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Service not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error updating service', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the service',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $deleted = $this->serviceService->delete($id);

            if (!$deleted) {
                return response()->json(
                    ['message' => 'Service not found.'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json([
                'message' => 'Service deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting service', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while deleting the service',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $services = $this->serviceService->list($searchDTO);

            return response()->json([
                'services' => [
                    'items' => ServiceResource::collection($services->items()),
                    'total_items' => $services->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error listing services', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while listing services',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByCategory(string $category): JsonResponse
    {
        try {
            $services = $this->serviceService->findByCategory($category);

            return response()->json([
                'services' => ServiceResource::collection($services),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching services by category', [
                'category' => $category,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching services',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleActive(string $id): JsonResponse
    {
        try {
            $service = $this->serviceService->toggleActive($id);

            return response()->json([
                'message' => 'Service status updated successfully',
                'service' => new ServiceResource($service),
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Service not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error toggling service status', [
                'service_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the service status',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listActive(Request $request): JsonResponse
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $services = $this->serviceService->listActive($searchDTO);

            return response()->json([
                'services' => [
                    'items' => ServiceResource::collection($services->items()),
                    'total_items' => $services->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error listing active services', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while listing active services',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
