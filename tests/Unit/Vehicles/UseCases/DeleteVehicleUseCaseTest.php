<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\DeleteVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use Tests\Fakes\InMemoryVehicleRepository;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

it('deletes a car by id', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);
    $out = $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $delete = new DeleteVehicleUseCase($repo);
    $delete->execute($out->carId);

    $all = $repo->all();
    expect(count($all))->toBe(0);
});

it('throws when deleting a non existing car', function () {
    $repo = new InMemoryVehicleRepository();
    $delete = new DeleteVehicleUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $delete->execute($fakeId);
})->throws(NotFoundException::class);
