<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\Domain\Entities;

use AppOficina\Vehicles\Entities\Vehicle;
use AppOficina\Vehicles\Exceptions\InvalidVehicleException;
use AppOficina\Vehicles\Exceptions\InvalidLicensePlateException;
use AppOficina\Vehicles\Exceptions\InvalidChassiException;
use Symfony\Component\Uid\Ulid;

// --- Vehicle Creation Tests ---
it('should create a car with valid data', function () {
    $clientId = new Ulid();
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: $clientId
    );

    expect($car)->toBeInstanceOf(Vehicle::class)
        ->and($car->brand)->toBe('Toyota')
        ->and($car->model)->toBe('Corolla')
        ->and($car->year)->toBe(2023)
        ->and($car->type())->toBe('car')
        ->and($car->clientId)->toBe($clientId)
        ->and($car->id)->toBeInstanceOf(Ulid::class);
});

it('should create a car with custom ID', function () {
    $customId = new Ulid();
    $clientId = new Ulid();
    $car = Vehicle::create(
        brand: 'Honda',
        model: 'Civic',
        year: 2022,
        vehicleType: "car",
        clientId: $clientId,
        id: $customId
    );

    expect($car->id)->toBe($customId)
        ->and($car->clientId)->toBe($clientId);
});

it('should create vehicles with different types', function (string $type) {
    $car = Vehicle::create(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        vehicleType: $type,
        clientId: new Ulid()
    );

    expect($car->type())->toBe($type);
})->with([
    'car',
    'motorcycle',
    'truck',
    'van',
    'bus'
]);

// --- Brand Validation Tests ---
it('should throw exception for empty brand', function () {
    expect(fn() => Vehicle::create(
        brand: '',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    ))->toThrow(InvalidVehicleException::class, 'Brand is required');
});

it('should throw exception for brand shorter than 3 characters', function (string $brand) {
    expect(fn() => Vehicle::create(
        brand: $brand,
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    ))->toThrow(InvalidVehicleException::class, 'Brand must be at least 3 characters long');
})->with(['a', 'ab']);

it('should accept brand with exactly 3 characters', function () {
    $car = Vehicle::create(
        brand: 'BMW',
        model: 'X1',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect($car->brand)->toBe('BMW');
});

// --- Model Validation Tests ---
it('should throw exception for empty model', function () {
    expect(fn() => Vehicle::create(
        brand: 'Toyota',
        model: '',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    ))->toThrow(InvalidVehicleException::class, 'Model is required');
});

it('should accept model with single character', function () {
    $car = Vehicle::create(
        brand: 'BMW',
        model: 'X',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect($car->model)->toBe('X');
});

// --- Year Validation Tests ---
it('should throw exception for year before 1886', function () {
    expect(fn() => Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 1885,
        vehicleType: "car",
        clientId: new Ulid()
    ))->toThrow(InvalidVehicleException::class, 'Year must be between 1886 and');
});

it('should throw exception for future year', function () {
    $futureYear = (int)date('Y') + 1;
    expect(fn() => Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: $futureYear,
        vehicleType: "car",
        clientId: new Ulid()
    ))->toThrow(InvalidVehicleException::class, 'Year must be between 1886 and');
});

it('should accept year 1886 (first car invention)', function () {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 1886,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect($car->year)->toBe(1886);
});

it('should accept current year', function () {
    $currentYear = (int)date('Y');
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: $currentYear,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect($car->year)->toBe($currentYear);
});

// --- Type Validation Tests ---
it('should throw exception for invalid car type', function () {
    expect(fn() => Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: 'invalid_type',
        clientId: new Ulid()
    ))->toThrow(\ValueError::class);
});

// --- License Plate Tests ---
it('should add license plate to car', function () {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    )->withLicencePlate('ABC1234');

    expect($car->licencePlate())->toBe('ABC1234');
});

it('should handle license plate with different formats', function (string $input, string $expected) {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    )->withLicencePlate($input);

    expect($car->licencePlate())->toBe($expected);
})->with([
    ['ABC1234', 'ABC1234'],
    ['ABC-1234', 'ABC1234'],
    ['abc1234', 'ABC1234'],
    ['ABC1D23', 'ABC1D23'],
]);

it('should throw exception for invalid license plate', function () {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect(fn() => $car->withLicencePlate('INVALID'))
        ->toThrow(InvalidLicensePlateException::class);
});

// --- VIN Tests ---
it('should add VIN to car', function () {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    )->withVin('1HGBH41JXMN109186');

    expect($car->vin())->toBe('1HGBH41JXMN109186');
});

it('should throw exception for invalid VIN', function () {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect(fn() => $car->withVin('INVALID'))
        ->toThrow(InvalidChassiException::class);
});

// --- Transmission Tests ---
it('should add transmission to car', function (string $transmission) {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    )->withTransmission($transmission);

    expect($car->transmission())->toBe($transmission);
})->with(['manual', 'automatic']);

it('should throw exception for invalid transmission', function () {
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    expect(fn() => $car->withTransmission('invalid'))
        ->toThrow(\ValueError::class);
});

// --- Immutability Tests ---
it('should return new instance when adding license plate', function () {
    $originalVehicle = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    $carWithPlate = $originalVehicle->withLicencePlate('ABC1234');

    expect($carWithPlate)->not->toBe($originalVehicle)
        ->and($originalVehicle->licencePlate())->toBe('')
        ->and($carWithPlate->licencePlate())->toBe('ABC1234');
});

it('should return new instance when adding VIN', function () {
    $originalVehicle = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    $carWithVin = $originalVehicle->withVin('1HGBH41JXMN109186');

    expect($carWithVin)->not->toBe($originalVehicle)
        ->and($originalVehicle->vin())->toBe('')
        ->and($carWithVin->vin())->toBe('1HGBH41JXMN109186');
});

it('should return new instance when adding transmission', function () {
    $originalVehicle = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: new Ulid()
    );

    $carWithTransmission = $originalVehicle->withTransmission('manual');

    expect($carWithTransmission)->not->toBe($originalVehicle)
        ->and($originalVehicle->transmission())->toBe('')
        ->and($carWithTransmission->transmission())->toBe('manual');
});

// --- Complex Operations Tests ---
it('should create a fully configured car', function () {
    $clientId = new Ulid();
    $car = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: $clientId
    )
        ->withLicencePlate('ABC1234')
        ->withVin('1HGBH41JXMN109186')
        ->withTransmission('automatic')
        ->withColor('Blue')
        ->withCilinderCapacity(1800)
        ->withMileage(50000)
        ->withObservations('Well maintained');

    expect($car->brand)->toBe('Toyota')
        ->and($car->model)->toBe('Corolla')
        ->and($car->year)->toBe(2023)
        ->and($car->type())->toBe('car')
        ->and($car->clientId)->toBe($clientId)
        ->and($car->licencePlate())->toBe('ABC1234')
        ->and($car->vin())->toBe('1HGBH41JXMN109186')
        ->and($car->transmission())->toBe('automatic')
        ->and($car->color)->toBe('Blue')
        ->and($car->cilinderCapacity)->toBe(1800)
        ->and($car->mileage)->toBe(50000)
        ->and($car->observations)->toBe('Well maintained');
});

it('should handle method chaining immutably', function () {
    $clientId = new Ulid();
    $originalVehicle = Vehicle::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        vehicleType: "car",
        clientId: $clientId
    );

    $modifiedVehicle = $originalVehicle
        ->withBrand('Honda')
        ->withModel('Civic')
        ->withYear(2022)
        ->withType('motorcycle');

    // Original should be unchanged
    expect($originalVehicle->brand)->toBe('Toyota')
        ->and($originalVehicle->model)->toBe('Corolla')
        ->and($originalVehicle->year)->toBe(2023)
        ->and($originalVehicle->type())->toBe('car');

    // Modified should have new values
    expect($modifiedVehicle->brand)->toBe('Honda')
        ->and($modifiedVehicle->model)->toBe('Civic')
        ->and($modifiedVehicle->year)->toBe(2022)
        ->and($modifiedVehicle->type())->toBe('motorcycle')
        ->and($modifiedVehicle->clientId)->toBe($clientId); // clientId should remain the same
});
