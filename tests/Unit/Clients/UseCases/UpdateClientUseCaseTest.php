<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\UseCases;

use AppOficina\Clients\UseCases\UpdateClient\UpdateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\CreateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\Input as CreateInput;
use AppOficina\Clients\UseCases\UpdateClient\Input as UpdateInput;
use Tests\Fakes\InMemoryClientRepository;
use AppOficina\Clients\Exceptions\ClientNotFoundException;
use Symfony\Component\Uid\Ulid;

it('updates client fields', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);
    $out = $create->execute(new CreateInput('John Doe', 'john@example.com', '42603972065'));

    $update = new UpdateClientUseCase($repo);
    $update->execute(new UpdateInput(clientId: $out->clientId, name: 'Jane Doe', email: 'jane@example.com'));

    $clients = $repo->all();
    expect($clients[0]->name)->toBe('Jane Doe')
        ->and($clients[0]->email)->toBe('jane@example.com');
});

it('throws when updating a non existing client', function () {
    $repo = new InMemoryClientRepository();
    $update = new UpdateClientUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $update->execute(new UpdateInput(clientId: $fakeId, name: 'X'));
})->throws(ClientNotFoundException::class);
