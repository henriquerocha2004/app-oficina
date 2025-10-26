<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\FindCarByVinUseCase;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use Tests\Fakes\InMemoryCarRepository;
use AppOficina\Cars\Exceptions\CarNotFoundException;

it('finds a car by VIN', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        vin: '1HGCM82633A123456'
    ));

    $find = new FindCarByVinUseCase($repo);

    $carOutput = $find->execute('1HGCM82633A123456');

    expect($carOutput['id'])->toBe($out->carId);
    expect($carOutput['brand'])->toBe('Toyota');
    expect($carOutput['model'])->toBe('Corolla');
});

it('throws when car with VIN is not found', function () {
    $repo = new InMemoryCarRepository();
    $find = new FindCarByVinUseCase($repo);

    $find->execute('INVALID_VIN_123');
})->throws(CarNotFoundException::class);
