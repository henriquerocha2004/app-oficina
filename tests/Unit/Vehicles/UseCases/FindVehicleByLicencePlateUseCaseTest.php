<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\FindVehicleByLicencePlateUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use Tests\Fakes\InMemoryVehicleRepository;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;

it('finds a car by licence plate', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        licencePlate: 'ABC-1234'
    ));

    $find = new FindVehicleByLicencePlateUseCase($repo);

    $carOutput = $find->execute('ABC-1234');

    expect($carOutput['id'])->toBe($out->carId);
    expect($carOutput['brand'])->toBe('Toyota');
    expect($carOutput['model'])->toBe('Corolla');
});

it('throws when car with licence plate is not found', function () {
    $repo = new InMemoryVehicleRepository();
    $find = new FindVehicleByLicencePlateUseCase($repo);

    $find->execute('XYZ-9999');
})->throws(VehicleNotFoundException::class);
