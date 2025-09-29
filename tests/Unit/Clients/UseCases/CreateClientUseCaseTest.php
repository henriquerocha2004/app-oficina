<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\UseCases;

use AppOficina\Clients\UseCases\CreateClient\CreateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\Input;
use Tests\Fakes\InMemoryClientRepository;

it('creates a new client when document not found', function () {
    $repo = new InMemoryClientRepository();
    $useCase = new CreateClientUseCase($repo);

    $input = new Input(
        name: 'John Doe',
        email: 'john@example.com',
        document: '42603972065'
    );

    $output = $useCase->execute($input);

    expect($output->clientId)->not->toBeEmpty();
    $all = $repo->all();
    expect(count($all))->toBe(1);
});

it('returns existing client id when document exists', function () {
    $repo = new InMemoryClientRepository();
    $useCase = new CreateClientUseCase($repo);

    $input1 = new Input('John Doe', 'john@example.com', '42603972065');
    $out1 = $useCase->execute($input1);

    $input2 = new Input('John Doe', 'john@example.com', '42603972065');
    $out2 = $useCase->execute($input2);

    expect($out1->clientId)->toBe($out2->clientId);
    $all = $repo->all();
    expect(count($all))->toBe(1);
});
