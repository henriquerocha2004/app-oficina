<?php

namespace App\Http\Controllers;

use App\DTOs\VehicleInputDTO;
use App\DTOs\SearchDTO;
use App\Services\VehicleService;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class VehiclesController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService
    ) {
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('vehicles/Index');
    }

    public function store(CreateVehicleRequest $request): JsonResponse
    {
        try {
            $dto = VehicleInputDTO::fromArray($request->validated());
            $vehicle = $this->vehicleService->create($dto);

            return response()->json([
                'message' => 'Vehicle created successfully',
                'vehicle_id' => $vehicle->id,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating vehicle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the vehicle',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showById(string $id): JsonResponse
    {
        try {
            $vehicle = $this->vehicleService->find($id);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'vehicle' => new VehicleResource($vehicle),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching vehicle', [
                'vehicle_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching the vehicle',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(string $id, UpdateVehicleRequest $request): JsonResponse
    {
        try {
            $dto = VehicleInputDTO::fromArray($request->validated());
            $this->vehicleService->update($id, $dto);

            return response()->json([
                'message' => 'Vehicle updated successfully',
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vehicle not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Error updating vehicle', [
                'vehicle_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the vehicle',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $deleted = $this->vehicleService->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Vehicle deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle', [
                'vehicle_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while deleting the vehicle',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByFilters(Request $request): JsonResponse
    {
        try {
            $searchDTO = SearchDTO::fromRequest($request);
            $vehicles = $this->vehicleService->list($searchDTO);

            return response()->json([
                'vehicles' => [
                    'items' => VehicleResource::collection($vehicles->items()),
                    'total_items' => $vehicles->total(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error listing vehicles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while listing vehicles',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByClientId(string $clientId): JsonResponse
    {
        try {
            $vehicles = $this->vehicleService->findByClientId($clientId);

            return response()->json([
                'vehicles' => VehicleResource::collection($vehicles),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching vehicles by client', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching vehicles',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByLicensePlate(string $licensePlate): JsonResponse
    {
        try {
            $vehicle = $this->vehicleService->findByLicensePlate($licensePlate);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'vehicle' => new VehicleResource($vehicle),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching vehicle by license plate', [
                'license_plate' => $licensePlate,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching the vehicle',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByVin(string $vin): JsonResponse
    {
        try {
            $vehicle = $this->vehicleService->findByVin($vin);

            if (!$vehicle) {
                return response()->json(['message' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'vehicle' => new VehicleResource($vehicle),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching vehicle by VIN', [
                'vin' => $vin,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching the vehicle',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
