<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\UseCases;

use AppOficina\Clients\UseCases\FindClientByIdUseCase;
use AppOficina\Clients\UseCases\CreateClient\CreateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\Input as CreateInput;
use Tests\Fakes\InMemoryClientRepository;
use Symfony\Component\Uid\Ulid;

use AppOficina\Clients\Exceptions\ClientNotFoundException;

it('finds a client by id', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);

    $out = $create->execute(new CreateInput('John Doe', 'john@example.com', '42603972065'));

    $find = new FindClientByIdUseCase($repo);

    $clientOutput = $find->execute($out->clientId);

    expect($clientOutput['id'])->toBe($out->clientId);
});

it('throws when client is not found', function () {
    $repo = new InMemoryClientRepository();
    $find = new FindClientByIdUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $find->execute($fakeId);
})->throws(ClientNotFoundException::class);
