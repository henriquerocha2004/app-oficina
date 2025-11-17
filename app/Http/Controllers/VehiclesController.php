<?php

namespace App\Http\Controllers;

use Throwable;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Requests\SearchInputRequest;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\FindVehicleByIdUseCase;
use AppOficina\Vehicles\UseCases\UpdateVehicle\UpdateVehicleUseCase;
use AppOficina\Vehicles\UseCases\DeleteVehicleUseCase;
use AppOficina\Vehicles\UseCases\ListVehiclesUseCase;
use AppOficina\Vehicles\UseCases\FindVehiclesByClientIdUseCase;
use AppOficina\Vehicles\UseCases\FindVehicleByLicencePlateUseCase;
use AppOficina\Vehicles\UseCases\FindVehicleByVinUseCase;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateVehicleInput;
use AppOficina\Vehicles\UseCases\UpdateVehicle\Input as UpdateVehicleInput;
use AppOficina\Shared\Search\SearchRequest;

class VehiclesController extends Controller
{
    public function __construct(
        private readonly CreateVehicleUseCase $createVehicleUseCase,
        private readonly FindVehicleByIdUseCase $findVehicleByIdUseCase,
        private readonly UpdateVehicleUseCase $updateVehicleUseCase,
        private readonly DeleteVehicleUseCase $deleteVehicleUseCase,
        private readonly ListVehiclesUseCase $listVehiclesUseCase,
        private readonly FindVehiclesByClientIdUseCase $findVehiclesByClientIdUseCase,
        private readonly FindVehicleByLicencePlateUseCase $findVehicleByLicencePlateUseCase,
        private readonly FindVehicleByVinUseCase $findVehicleByVinUseCase
    ) {
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('vehicles/Index');
    }

    public function store(CreateVehicleRequest $request): JsonResponse
    {
        try {
            $input = new CreateVehicleInput(
                brand: $request->validated('brand'),
                model: $request->validated('model'),
                year: $request->validated('year'),
                type: $request->validated('typeVehicle'),
                clientId: $request->validated('clientId'),
                licensePlate: $request->validated('licensePlate'),
                vin: $request->validated('vin'),
                color: $request->validated('color'),
                mileage: $request->validated('mileage'),
                transmission: $request->validated('transmission'),
                cilinderCapacity: $request->validated('engineSize'),
                observations: $request->validated('observations'),
            );

            $output = $this->createVehicleUseCase->execute($input);

            return response()->json([
                'message' => 'Vehicle created successfully',
                'car_id' => $output->carId,
            ], Response::HTTP_CREATED);
        } catch (Throwable $throwable) {
            Log::error('Error creating car: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Error creating car',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showById(string $id): JsonResponse
    {
        try {
            $car = $this->findVehicleByIdUseCase->execute($id);

            return response()->json([
                'car' => $car,
            ], Response::HTTP_OK);
        } catch (VehicleNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error fetching car: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'car_id' => $id,
            ]);

            return response()->json([
                'message' => 'Error fetching car',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(string $id, UpdateVehicleRequest $request): JsonResponse
    {
        try {
            $input = new UpdateVehicleInput(
                carId: $id,
                brand: $request->validated('brand'),
                model: $request->validated('model'),
                year: $request->validated('year'),
                type: $request->validated('typeVehicle'),
                clientId: $request->validated('clientId'),
                licencePlate: $request->validated('licencePlate'),
                vin: $request->validated('vin'),
                color: $request->validated('color'),
                mileage: $request->validated('mileage'),
                transmission: $request->validated('transmission'),
                cilinderCapacity: $request->validated('engineSize'),
                observations: $request->validated('observations'),
            );

            $this->updateVehicleUseCase->execute($input);

            return response()->json([
                'message' => 'Vehicle updated successfully',
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error('Error updating car: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'car_id' => $id,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Error updating car',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->deleteVehicleUseCase->execute($id);

            return response()->json([
                'message' => 'Vehicle deleted successfully',
            ], Response::HTTP_OK);
        } catch (VehicleNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error deleting car: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'car_id' => $id,
            ]);

            return response()->json([
                'message' => 'Error deleting car',
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
                search: $request->validated('search') ?? '',
                columnSearch: $request->validated('columnSearch', []),
            );

            $cars = $this->listVehiclesUseCase->execute($filters);

            return response()->json([
                'vehicles' => $cars,
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error('Error fetching cars by filters: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'filters' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Error fetching cars by filters',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByClientId(string $clientId): JsonResponse
    {
        try {
            $cars = $this->findVehiclesByClientIdUseCase->execute($clientId);

            return response()->json([
                'vehicles' => $cars,
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error('Error fetching cars by client ID: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'client_id' => $clientId,
            ]);

            return response()->json([
                'message' => 'Error fetching cars by client ID',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByLicencePlate(string $licencePlate): JsonResponse
    {
        try {
            $car = $this->findVehicleByLicencePlateUseCase->execute($licencePlate);

            return response()->json([
                'car' => $car,
            ], Response::HTTP_OK);
        } catch (VehicleNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error fetching car by licence plate: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'license_plate' => $licencePlate,
            ]);

            return response()->json([
                'message' => 'Error fetching car by licence plate',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByVin(string $vin): JsonResponse
    {
        try {
            $car = $this->findVehicleByVinUseCase->execute($vin);

            return response()->json([
                'car' => $car,
            ], Response::HTTP_OK);
        } catch (VehicleNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error fetching car by VIN: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'vin' => $vin,
            ]);

            return response()->json([
                'message' => 'Error fetching car by VIN',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
