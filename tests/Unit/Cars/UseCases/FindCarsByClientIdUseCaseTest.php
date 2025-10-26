<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\FindCarsByClientIdUseCase;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use Tests\Fakes\InMemoryCarRepository;
use Symfony\Component\Uid\Ulid;

it('finds cars by client id', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

    $clientId = (string) Ulid::generate();

    $car1 = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: $clientId
    ));
    $car2 = $create->execute(new CreateInput(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $car3 = $create->execute(new CreateInput(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        type: 'hatchback',
        clientId: $clientId
    ));

    $find = new FindCarsByClientIdUseCase($repo);

    $cars = $find->execute($clientId);

    expect(count($cars))->toBe(2);
    expect($cars[0]['id'])->toBe($car1->carId);
    expect($cars[1]['id'])->toBe($car3->carId);
});

it('returns empty array when client has no cars', function () {
    $repo = new InMemoryCarRepository();
    $find = new FindCarsByClientIdUseCase($repo);

    $clientId = (string) Ulid::generate();

    $cars = $find->execute($clientId);

    expect(count($cars))->toBe(0);
});

it('returns empty array when client does not exist', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

    // Create some cars but don't associate with any client
    $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $create->execute(new CreateInput(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $find = new FindCarsByClientIdUseCase($repo);
    $fakeClientId = (string) Ulid::generate();

    $cars = $find->execute($fakeClientId);

    expect(count($cars))->toBe(0);
});
