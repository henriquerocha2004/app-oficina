<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\FindVehicleByIdUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use Tests\Fakes\InMemoryVehicleRepository;
use Symfony\Component\Uid\Ulid;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;

it('finds a car by id', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $find = new FindVehicleByIdUseCase($repo);

    $carOutput = $find->execute($out->carId);

    expect($carOutput['id'])->toBe($out->carId);
    expect($carOutput['brand'])->toBe('Toyota');
    expect($carOutput['model'])->toBe('Corolla');
});

it('throws when car is not found', function () {
    $repo = new InMemoryVehicleRepository();
    $find = new FindVehicleByIdUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $find->execute($fakeId);
})->throws(VehicleNotFoundException::class);
