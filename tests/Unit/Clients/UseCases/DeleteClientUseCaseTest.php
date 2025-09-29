<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\UseCases;

use AppOficina\Clients\UseCases\DeleteClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\CreateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\Input as CreateInput;
use Tests\Fakes\InMemoryClientRepository;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

it('deletes a client by id', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);
    $out = $create->execute(new CreateInput('John Doe', 'john@example.com', '42603972065'));

    $delete = new DeleteClientUseCase($repo);
    $delete->execute($out->clientId);

    $all = $repo->all();
    expect(count($all))->toBe(0);
});

it('throws when deleting a non existing client', function () {
    $repo = new InMemoryClientRepository();
    $delete = new DeleteClientUseCase($repo);

    $fakeId = (string) Ulid::generate();

    $delete->execute($fakeId);
})->throws(NotFoundException::class);
