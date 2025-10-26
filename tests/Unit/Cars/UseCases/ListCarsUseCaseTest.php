<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\UseCases;

use AppOficina\Cars\UseCases\ListCarsUseCase;
use AppOficina\Cars\UseCases\CreateCar\CreateCarUseCase;
use AppOficina\Cars\UseCases\CreateCar\Input as CreateInput;
use Tests\Fakes\InMemoryCarRepository;
use AppOficina\Shared\Search\SearchRequest;

it('lists all cars', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

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
    $create->execute(new CreateInput(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        type: 'hatchback',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $list = new ListCarsUseCase($repo);
    $searchRequest = new SearchRequest(limit: 10);

    $response = $list->execute($searchRequest);

    expect($response->totalItems)->toBe(3);
    expect(count($response->items))->toBe(3);
});

it('returns empty list when no cars exist', function () {
    $repo = new InMemoryCarRepository();
    $list = new ListCarsUseCase($repo);
    $searchRequest = new SearchRequest(limit: 10);

    $response = $list->execute($searchRequest);

    expect($response->totalItems)->toBe(0);
    expect(count($response->items))->toBe(0);
});

it('respects pagination limit', function () {
    $repo = new InMemoryCarRepository();
    $create = new CreateCarUseCase($repo);

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
    $create->execute(new CreateInput(
        brand: 'Ford',
        model: 'Focus',
        year: 2021,
        type: 'hatchback',
        clientId: '01HKQX1GQHJ7XK5W8YXJP2GX9F'
    ));

    $list = new ListCarsUseCase($repo);
    $searchRequest = new SearchRequest(limit: 2, page: 1);

    $response = $list->execute($searchRequest);

    expect($response->totalItems)->toBe(3);
    expect(count($response->items))->toBe(2);
});
