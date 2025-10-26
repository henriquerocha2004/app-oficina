<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\DeleteCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use Tests\Fakes\InMemoryCarRepository;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

it('deletes a car by id', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);
    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: 'sedan',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $delete = new DeleteCarUseCase($repo);
    $delete->execute($out->carId);

    $all = $repo->all();
    expect(count($all))->toBe(0);
});

it('throws when deleting a non existing car', function () {
    $repo = new InMemoryCarRepository();
    $delete = new DeleteCarUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $delete->execute($fakeId);
})->throws(NotFoundException::class);
