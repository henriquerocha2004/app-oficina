<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\Domain\Entities;

use AppOficina\Cars\Entities\Car;
use AppOficina\Cars\Exceptions\InvalidCarException;
use AppOficina\Cars\Exceptions\InvalidLicensePlateException;
use AppOficina\Cars\Exceptions\InvalidChassiException;
use Symfony\Component\Uid\Ulid;

// --- Car Creation Tests ---
it('should create a car with valid data', function () {
    $clientId = new Ulid();
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: $clientId
    );

    expect($car)->toBeInstanceOf(Car::class)
        ->and($car->brand)->toBe('Toyota')
        ->and($car->model)->toBe('Corolla')
        ->and($car->year)->toBe(2023)
        ->and($car->type())->toBe('sedan')
        ->and($car->clientId)->toBe($clientId)
        ->and($car->id)->toBeInstanceOf(Ulid::class);
});

it('should create a car with custom ID', function () {
    $customId = new Ulid();
    $clientId = new Ulid();
    $car = Car::create(
        brand: 'Honda',
        model: 'Civic',
        year: 2022,
        typeCar: 'hatchback',
        clientId: $clientId,
        id: $customId
    );

    expect($car->id)->toBe($customId)
        ->and($car->clientId)->toBe($clientId);
});

it('should create cars with different types', function (string $type) {
    $car = Car::create(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        typeCar: $type,
        clientId: new Ulid()
    );

    expect($car->type())->toBe($type);
})->with([
    'sedan',
    'hatchback',
    'suv',
    'coupe',
    'convertible',
    'wagon',
    'van',
    'pickup'
]);

// --- Brand Validation Tests ---
it('should throw exception for empty brand', function () {
    expect(fn() => Car::create(
        brand: '',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    ))->toThrow(InvalidCarException::class, 'Brand is required');
});

it('should throw exception for brand shorter than 3 characters', function (string $brand) {
    expect(fn() => Car::create(
        brand: $brand,
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    ))->toThrow(InvalidCarException::class, 'Brand must be at least 3 characters long');
})->with(['a', 'ab']);

it('should accept brand with exactly 3 characters', function () {
    $car = Car::create(
        brand: 'BMW',
        model: 'X1',
        year: 2023,
        typeCar: 'suv',
        clientId: new Ulid()
    );

    expect($car->brand)->toBe('BMW');
});

// --- Model Validation Tests ---
it('should throw exception for empty model', function () {
    expect(fn() => Car::create(
        brand: 'Toyota',
        model: '',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    ))->toThrow(InvalidCarException::class, 'Model is required');
});

it('should accept model with single character', function () {
    $car = Car::create(
        brand: 'BMW',
        model: 'X',
        year: 2023,
        typeCar: 'suv',
        clientId: new Ulid()
    );

    expect($car->model)->toBe('X');
});

// --- Year Validation Tests ---
it('should throw exception for year before 1886', function () {
    expect(fn() => Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 1885,
        typeCar: 'sedan',
        clientId: new Ulid()
    ))->toThrow(InvalidCarException::class, 'Year must be between 1886 and');
});

it('should throw exception for future year', function () {
    $futureYear = (int)date('Y') + 1;
    expect(fn() => Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: $futureYear,
        typeCar: 'sedan',
        clientId: new Ulid()
    ))->toThrow(InvalidCarException::class, 'Year must be between 1886 and');
});

it('should accept year 1886 (first car invention)', function () {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 1886,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    expect($car->year)->toBe(1886);
});

it('should accept current year', function () {
    $currentYear = (int)date('Y');
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: $currentYear,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    expect($car->year)->toBe($currentYear);
});

// --- Type Validation Tests ---
it('should throw exception for invalid car type', function () {
    expect(fn() => Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'invalid_type',
        clientId: new Ulid()
    ))->toThrow(\ValueError::class);
});

// --- License Plate Tests ---
it('should add license plate to car', function () {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    )->withLicencePlate('ABC1234');

    expect($car->licencePlate())->toBe('ABC1234');
});

it('should handle license plate with different formats', function (string $input, string $expected) {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
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
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    expect(fn() => $car->withLicencePlate('INVALID'))
        ->toThrow(InvalidLicensePlateException::class);
});

// --- VIN Tests ---
it('should add VIN to car', function () {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    )->withVin('1HGBH41JXMN109186');

    expect($car->vin())->toBe('1HGBH41JXMN109186');
});

it('should throw exception for invalid VIN', function () {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    expect(fn() => $car->withVin('INVALID'))
        ->toThrow(InvalidChassiException::class);
});

// --- Transmission Tests ---
it('should add transmission to car', function (string $transmission) {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    )->withTransmission($transmission);

    expect($car->transmission())->toBe($transmission);
})->with(['manual', 'automatic']);

it('should throw exception for invalid transmission', function () {
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    expect(fn() => $car->withTransmission('invalid'))
        ->toThrow(\ValueError::class);
});

// --- Immutability Tests ---
it('should return new instance when adding license plate', function () {
    $originalCar = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    $carWithPlate = $originalCar->withLicencePlate('ABC1234');

    expect($carWithPlate)->not->toBe($originalCar)
        ->and($originalCar->licencePlate())->toBe('')
        ->and($carWithPlate->licencePlate())->toBe('ABC1234');
});

it('should return new instance when adding VIN', function () {
    $originalCar = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    $carWithVin = $originalCar->withVin('1HGBH41JXMN109186');

    expect($carWithVin)->not->toBe($originalCar)
        ->and($originalCar->vin())->toBe('')
        ->and($carWithVin->vin())->toBe('1HGBH41JXMN109186');
});

it('should return new instance when adding transmission', function () {
    $originalCar = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: new Ulid()
    );

    $carWithTransmission = $originalCar->withTransmission('manual');

    expect($carWithTransmission)->not->toBe($originalCar)
        ->and($originalCar->transmission())->toBe('')
        ->and($carWithTransmission->transmission())->toBe('manual');
});

// --- Complex Operations Tests ---
it('should create a fully configured car', function () {
    $clientId = new Ulid();
    $car = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
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
        ->and($car->type())->toBe('sedan')
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
    $originalCar = Car::create(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2023,
        typeCar: 'sedan',
        clientId: $clientId
    );

    $modifiedCar = $originalCar
        ->withBrand('Honda')
        ->withModel('Civic')
        ->withYear(2022)
        ->withType('hatchback');

    // Original should be unchanged
    expect($originalCar->brand)->toBe('Toyota')
        ->and($originalCar->model)->toBe('Corolla')
        ->and($originalCar->year)->toBe(2023)
        ->and($originalCar->type())->toBe('sedan');

    // Modified should have new values
    expect($modifiedCar->brand)->toBe('Honda')
        ->and($modifiedCar->model)->toBe('Civic')
        ->and($modifiedCar->year)->toBe(2022)
        ->and($modifiedCar->type())->toBe('hatchback')
        ->and($modifiedCar->clientId)->toBe($clientId); // clientId should remain the same
});
