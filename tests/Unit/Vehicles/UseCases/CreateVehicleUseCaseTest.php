<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input;
use Tests\Fakes\InMemoryVehicleRepository;

it('creates a new car', function () {
    $repo = new InMemoryVehicleRepository();
    $useCase = new CreateVehicleUseCase($repo);

    $input = new Input(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    );

    $output = $useCase->execute($input);

    expect($output->carId)->not->toBeEmpty();
    $all = $repo->all();
    expect(count($all))->toBe(1);

    $car = $all[0];
    expect($car->clientId->__toString())->toBe('01HKQX1GQHJ7XK5W8YXJP2GX9F');
});

it('creates a car with all optional fields', function () {
    $repo = new InMemoryVehicleRepository();
    $useCase = new CreateVehicleUseCase($repo);

    $input = new Input(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        licencePlate: 'ABC-1234',
        vin: '1HGCM82633A123456',
        transmission: 'manual',
        color: 'Blue',
        cilinderCapacity: 1800,
        mileage: 50000,
        observations: 'Vehicle in good condition'
    );

    $output = $useCase->execute($input);

    expect($output->carId)->not->toBeEmpty();
    $all = $repo->all();
    expect(count($all))->toBe(1);

    $car = $all[0];
    expect($car->clientId->__toString())->toBe('01HKQX1GQHJ7XK5W8YXJP2GX9F');
    expect($car->brand)->toBe('Honda');
    expect($car->model)->toBe('Civic');
    expect($car->year)->toBe(2023);
    expect($car->licencePlate())->toBe('ABC1234');
    expect($car->vin())->toBe('1HGCM82633A123456');
    expect($car->transmission())->toBe('manual');
});

it('returns existing car id when licence plate exists', function () {
    $repo = new InMemoryVehicleRepository();
    $useCase = new CreateVehicleUseCase($repo);

    $input1 = new Input(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        licencePlate: 'ABC-1234'
    );
    $out1 = $useCase->execute($input1);

    $input2 = new Input(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9G',
        licencePlate: 'ABC-1234'
    );
    $out2 = $useCase->execute($input2);

    expect($out1->carId)->toBe($out2->carId);
    $all = $repo->all();
    expect(count($all))->toBe(1);
});

it('returns existing car id when VIN exists', function () {
    $repo = new InMemoryVehicleRepository();
    $useCase = new CreateVehicleUseCase($repo);

    $input1 = new Input(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        vin: '1HGCM82633A123456'
    );
    $out1 = $useCase->execute($input1);

    $input2 = new Input(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9G',
        vin: '1HGCM82633A123456'
    );
    $out2 = $useCase->execute($input2);

    expect($out1->carId)->toBe($out2->carId);
    $all = $repo->all();
    expect(count($all))->toBe(1);
});
