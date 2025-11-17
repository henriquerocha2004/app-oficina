<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\FindVehicleByVinUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use Tests\Fakes\InMemoryVehicleRepository;
use AppOficina\Vehicles\Exceptions\VehicleNotFoundException;

it('finds a car by VIN', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        vin: '1HGCM82633A123456'
    ));

    $find = new FindVehicleByVinUseCase($repo);

    $carOutput = $find->execute('1HGCM82633A123456');

    expect($carOutput['id'])->toBe($out->carId);
    expect($carOutput['brand'])->toBe('Toyota');
    expect($carOutput['model'])->toBe('Corolla');
});

it('throws when car with VIN is not found', function () {
    $repo = new InMemoryVehicleRepository();
    $find = new FindVehicleByVinUseCase($repo);

    $find->execute('INVALID_VIN_123');
})->throws(VehicleNotFoundException::class);
