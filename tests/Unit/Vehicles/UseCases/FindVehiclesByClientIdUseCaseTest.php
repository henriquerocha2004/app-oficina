<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\FindVehiclesByClientIdUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use Tests\Fakes\InMemoryVehicleRepository;
use Symfony\Component\Uid\Ulid;

it('finds vehicles by client id', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    $clientId = (string) Ulid::generate();

    $car1 = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: $clientId
    ));
    $car2 = $create->execute(new CreateInput(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $car3 = $create->execute(new CreateInput(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        type: "car",
        clientId: $clientId
    ));

    $find = new FindVehiclesByClientIdUseCase($repo);

    $vehicles = $find->execute($clientId);

    expect(count($vehicles))->toBe(2);
    expect($vehicles[0]['id'])->toBe($car1->carId);
    expect($vehicles[1]['id'])->toBe($car3->carId);
});

it('returns empty array when client has no vehicles', function () {
    $repo = new InMemoryVehicleRepository();
    $find = new FindVehiclesByClientIdUseCase($repo);

    $clientId = (string) Ulid::generate();

    $vehicles = $find->execute($clientId);

    expect(count($vehicles))->toBe(0);
});

it('returns empty array when client does not exist', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    // Create some vehicles but don't associate with any client
    $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $create->execute(new CreateInput(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $find = new FindVehiclesByClientIdUseCase($repo);
    $fakeClientId = (string) Ulid::generate();

    $vehicles = $find->execute($fakeClientId);

    expect(count($vehicles))->toBe(0);
});
