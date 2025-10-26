<?php

namespace App\Http\Controllers;

use Throwable;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Requests\SearchInputRequest;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\FindCarByIdUseCase;
use AppOficina\Cars\UseCases\UpdateCar\UpdateCarUseCase;
use AppOficina\Cars\UseCases\DeleteCarUseCase;
use AppOficina\Cars\UseCases\ListCarsUseCase;
use AppOficina\Cars\UseCases\FindCarsByClientIdUseCase;
use AppOficina\Cars\UseCases\FindCarByLicencePlateUseCase;
use AppOficina\Cars\UseCases\FindCarByVinUseCase;
use AppOficina\Cars\Exceptions\CarNotFoundException;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateCarInput;
use AppOficina\Cars\UseCases\UpdateCar\Input as UpdateCarInput;
use AppOficina\Shared\Search\SearchRequest;

class CarsController extends Controller
{
    public function __construct(
        private readonly CreateCarUseCase $createCarUseCase,
        private readonly FindCarByIdUseCase $findCarByIdUseCase,
        private readonly UpdateCarUseCase $updateCarUseCase,
        private readonly DeleteCarUseCase $deleteCarUseCase,
        private readonly ListCarsUseCase $listCarsUseCase,
        private readonly FindCarsByClientIdUseCase $findCarsByClientIdUseCase,
        private readonly FindCarByLicencePlateUseCase $findCarByLicencePlateUseCase,
        private readonly FindCarByVinUseCase $findCarByVinUseCase
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $filters = new SearchRequest(
                page: 1,
                limit: 50,
                sort: 'asc',
                sortField: 'brand',
                search: '',
                columnSearch: [],
            );

            $cars = $this->listCarsUseCase->execute($filters);

            return response()->json([
                'cars' => $cars,
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error('Error listing cars: ' . $throwable->getMessage(), [
                'exception' => $throwable,
            ]);

            return response()->json([
                'message' => 'Error listing cars',
                'error' => $throwable->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(CreateCarRequest $request): JsonResponse
    {
        try {
            $input = new CreateCarInput(
                brand: $request->validated('brand'),
                model: $request->validated('model'),
                year: $request->validated('year'),
                type: $request->validated('typeCar'),
                clientId: $request->validated('clientId'),
                licencePlate: $request->validated('licencePlate'),
                vin: $request->validated('vin'),
                color: $request->validated('color'),
                mileage: $request->validated('mileage'),
                transmission: $request->validated('transmission'),
                cilinderCapacity: $request->validated('engineSize'),
                observations: $request->validated('observations'),
            );

            $output = $this->createCarUseCase->execute($input);

            return response()->json([
                'message' => 'Car created successfully',
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
            $car = $this->findCarByIdUseCase->execute($id);

            return response()->json([
                'car' => $car,
            ], Response::HTTP_OK);
        } catch (CarNotFoundException $exception) {
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

    public function update(string $id, UpdateCarRequest $request): JsonResponse
    {
        try {
            $input = new UpdateCarInput(
                carId: $id,
                brand: $request->validated('brand'),
                model: $request->validated('model'),
                year: $request->validated('year'),
                type: $request->validated('typeCar'),
                clientId: $request->validated('clientId'),
                licencePlate: $request->validated('licencePlate'),
                vin: $request->validated('vin'),
                color: $request->validated('color'),
                mileage: $request->validated('mileage'),
                transmission: $request->validated('transmission'),
                cilinderCapacity: $request->validated('engineSize'),
                observations: $request->validated('observations'),
            );

            $this->updateCarUseCase->execute($input);

            return response()->json([
                'message' => 'Car updated successfully',
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
            $this->deleteCarUseCase->execute($id);

            return response()->json([
                'message' => 'Car deleted successfully',
            ], Response::HTTP_OK);
        } catch (CarNotFoundException $exception) {
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

            $cars = $this->listCarsUseCase->execute($filters);

            return response()->json([
                'cars' => $cars,
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
            $cars = $this->findCarsByClientIdUseCase->execute($clientId);

            return response()->json([
                'cars' => $cars,
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
            $car = $this->findCarByLicencePlateUseCase->execute($licencePlate);

            return response()->json([
                'car' => $car,
            ], Response::HTTP_OK);
        } catch (CarNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (Throwable $throwable) {
            Log::error('Error fetching car by licence plate: ' . $throwable->getMessage(), [
                'exception' => $throwable,
                'licence_plate' => $licencePlate,
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
            $car = $this->findCarByVinUseCase->execute($vin);

            return response()->json([
                'car' => $car,
            ], Response::HTTP_OK);
        } catch (CarNotFoundException $exception) {
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
