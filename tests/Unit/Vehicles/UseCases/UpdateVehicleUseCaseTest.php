<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\UpdateVehicle\UpdateVehicleUseCase;
use AppOficina\Vehicles\UseCases\UpdateVehicle\Input as UpdateInput;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use AppOficina\Vehicles\UseCases\FindVehicleByIdUseCase;
use Tests\Fakes\InMemoryVehicleRepository;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;
use Symfony\Component\Uid\Ulid;

it('updates a car with new data', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);
    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $update = new UpdateVehicleUseCase($repo);
    $updateInput = new UpdateInput(
        carId: $out->carId,
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        color: 'Blue',
        mileage: 50000
    );

    $update->execute($updateInput);

    $find = new FindVehicleByIdUseCase($repo);
    $carData = $find->execute($out->carId);

    expect($carData['brand'])->toBe('Honda');
    expect($carData['model'])->toBe('Civic');
    expect($carData['year'])->toBe(2023);
});

it('updates only provided fields', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);
    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $update = new UpdateVehicleUseCase($repo);
    $updateInput = new UpdateInput(
        carId: $out->carId,
        color: 'Red'
    );

    $update->execute($updateInput);

    $find = new FindVehicleByIdUseCase($repo);
    $carData = $find->execute($out->carId);

    expect($carData['brand'])->toBe('Toyota'); // unchanged
    expect($carData['model'])->toBe('Corolla'); // unchanged
    expect($carData['year'])->toBe(2022); // unchanged
});

it('throws when updating a non existing car', function () {
    $repo = new InMemoryVehicleRepository();
    $update = new UpdateVehicleUseCase($repo);

    $fakeId = (string) Ulid::generate();
    $updateInput = new UpdateInput(
        carId: $fakeId,
        brand: 'Honda'
    );

    $update->execute($updateInput);
})->throws(VehicleNotFoundException::class);
