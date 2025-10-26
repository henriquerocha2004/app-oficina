<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\FindCarByIdUseCase;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use Tests\Fakes\InMemoryCarRepository;
use Symfony\Component\Uid\Ulid;
use AppOficina\Cars\Exceptions\CarNotFoundException;

it('finds a car by id', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $find = new FindCarByIdUseCase($repo);

    $carOutput = $find->execute($out->carId);

    expect($carOutput['id'])->toBe($out->carId);
    expect($carOutput['brand'])->toBe('Toyota');
    expect($carOutput['model'])->toBe('Corolla');
});

it('throws when car is not found', function () {
    $repo = new InMemoryCarRepository();
    $find = new FindCarByIdUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $find->execute($fakeId);
})->throws(CarNotFoundException::class);
