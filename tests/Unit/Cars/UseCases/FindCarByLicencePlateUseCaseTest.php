<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\FindCarByLicencePlateUseCase;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use Tests\Fakes\InMemoryCarRepository;
use AppOficina\Cars\Exceptions\CarNotFoundException;

it('finds a car by licence plate', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F',
        licencePlate: 'ABC-1234'
    ));

    $find = new FindCarByLicencePlateUseCase($repo);

    $carOutput = $find->execute('ABC-1234');

    expect($carOutput['id'])->toBe($out->carId);
    expect($carOutput['brand'])->toBe('Toyota');
    expect($carOutput['model'])->toBe('Corolla');
});

it('throws when car with licence plate is not found', function () {
    $repo = new InMemoryCarRepository();
    $find = new FindCarByLicencePlateUseCase($repo);

    $find->execute('XYZ-9999');
})->throws(CarNotFoundException::class);
