<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\UseCases;

use AppOficina\Vehicles\UseCases\ListVehiclesUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\CreateVehicleUseCase;
use AppOficina\Vehicles\UseCases\CreateVehicle\Input as CreateInput;
use Tests\Fakes\InMemoryVehicleRepository;
use AppOficina\Shared\Search\SearchRequest;

it('lists all vehicles', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $create->execute(new CreateInput(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $create->execute(new CreateInput(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $list = new ListVehiclesUseCase($repo);
    $searchRequest = new SearchRequest(limit: 10);

    $response = $list->execute($searchRequest);

    expect($response->totalItems)->toBe(3);
    expect(count($response->items))->toBe(3);
});

it('returns empty list when no vehicles exist', function () {
    $repo = new InMemoryVehicleRepository();
    $list = new ListVehiclesUseCase($repo);
    $searchRequest = new SearchRequest(limit: 10);

    $response = $list->execute($searchRequest);

    expect($response->totalItems)->toBe(0);
    expect(count($response->items))->toBe(0);
});

it('respects pagination limit', function () {
    $repo = new InMemoryVehicleRepository();
    $create = new CreateVehicleUseCase($repo);

    $create->execute(new CreateInput(
        brand: 'Toyota',
        model: 'Corolla',
        year: 2022,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $create->execute(new CreateInput(
        brand: 'Honda',
        model: 'Civic',
        year: 2023,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));
    $create->execute(new CreateInput(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        type: "car",
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $list = new ListVehiclesUseCase($repo);
    $searchRequest = new SearchRequest(limit: 2, page: 1);

    $response = $list->execute($searchRequest);

    expect($response->totalItems)->toBe(3);
    expect(count($response->items))->toBe(2);
});
