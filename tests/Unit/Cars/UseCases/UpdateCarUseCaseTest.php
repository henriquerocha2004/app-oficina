<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\UpdateCar\UpdateCarUseCase;
use AppOficina\Cars\UseCases\UpdateCar\Input as UpdateInput;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use AppOficina\Cars\UseCases\FindCarByIdUseCase;
use Tests\Fakes\InMemoryCarRepository;
use AppOficina\Cars\Exceptions\CarNotFoundException;
use Symfony\Component\Uid\Ulid;

it('updates a car with new data', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);
    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $update = new UpdateCarUseCase($repo);
    $updateInput = new UpdateInput(
        carId: $out->carId,
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        color: 'Blue',
        mileage: 50000
    );

    $update->execute($updateInput);

    $find = new FindCarByIdUseCase($repo);
    $carData = $find->execute($out->carId);

    expect($carData['brand'])->toBe('Honda');
    expect($carData['model'])->toBe('Civic');
    expect($carData['year'])->toBe(2023);
});

it('updates only provided fields', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);
    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $update = new UpdateCarUseCase($repo);
    $updateInput = new UpdateInput(
        carId: $out->carId,
        color: 'Red'
    );

    $update->execute($updateInput);

    $find = new FindCarByIdUseCase($repo);
    $carData = $find->execute($out->carId);

    expect($carData['brand'])->toBe('Toyota'); // unchanged
    expect($carData['model'])->toBe('Corolla'); // unchanged
    expect($carData['year'])->toBe(2022); // unchanged
});

it('throws when updating a non existing car', function () {
    $repo = new InMemoryCarRepository();
    $update = new UpdateCarUseCase($repo);

    $fakeId = (string) Ulid::generate();
    $updateInput = new UpdateInput(
        carId: $fakeId,
        brand: 'Honda'
    );

    $update->execute($updateInput);
})->throws(CarNotFoundException::class);
