<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\UseCases;

use AppOficina\Clients\UseCases\ListClientsUseCase;
use AppOficina\Clients\UseCases\CreateClient\CreateClientUseCase;
use AppOficina\Clients\UseCases\CreateClient\Input as CreateInput;
use AppOficina\Shared\Search\SearchRequest;
use Tests\Fakes\InMemoryClientRepository;
use AppOficina\Shared\Search\SearchResponse;

it('lists clients using search request', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);
    $create->execute(new CreateInput('A', 'a@example.com', '42603972065'));
    $create->execute(new CreateInput('B', 'b@example.com', '64361235000191'));

    $list = new ListClientsUseCase($repo);
    /** @var SearchResponse $resp */
    $resp = $list->execute(new SearchRequest());

    expect($resp)->toBeInstanceOf(SearchResponse::class)
        ->and($resp->totalItems)->toBe(2);
});

it('paginates results correctly', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);

    // create 5 clients using known-valid documents
    $docs = ['42603972065', '67887286077', '15540258002', '64361235000191', '20825707000144'];
    foreach ($docs as $i => $doc) {
        $create->execute(new CreateInput('User' . ($i + 1), "user{$i}@example.com", $doc));
    }

    $list = new ListClientsUseCase($repo);
    /** @var SearchResponse $resp */
    $resp = $list->execute(new SearchRequest(limit: 2, search: '', sortField: 'name', sort: 'asc', page: 2));

    expect($resp->totalItems)->toBe(5)
        ->and(count($resp->items))->toBe(2);
});

it('filters by search term', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);

    $create->execute(new CreateInput('Alice', 'alice@example.com', '42603972065'));
    $create->execute(new CreateInput('Bob', 'bob@example.com', '64361235000191'));

    $list = new ListClientsUseCase($repo);
    /** @var SearchResponse $resp */
    $resp = $list->execute(new SearchRequest(limit: 10, search: 'alice'));

    expect($resp->totalItems)->toBe(1)
        ->and($resp->items[0]->name)->toBe('Alice');
});

it('filters by column search', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);

    $create->execute(new CreateInput('Vehiclelos', 'carlos@example.com', '42603972065'));
    $create->execute(new CreateInput('Diego', 'diego@example.com', '64361235000191'));

    $list = new ListClientsUseCase($repo);
    $req = new SearchRequest(limit: 10, search: '', sortField: 'id', sort: 'asc', page: 1, columnSearch: ['email' => 'diego@example.com']);
    /** @var SearchResponse $resp */
    $resp = $list->execute($req);

    expect($resp->totalItems)->toBe(1)
        ->and($resp->items[0]->email)->toBe('diego@example.com');
});

it('sorts results by document descending', function () {
    $repo = new InMemoryClientRepository();
    $create = new CreateClientUseCase($repo);

    // use valid CPF numbers
    $create->execute(new CreateInput('X', 'x@example.com', '42603972065'));
    $create->execute(new CreateInput('Y', 'y@example.com', '67887286077'));

    $list = new ListClientsUseCase($repo);
    /** @var SearchResponse $resp */
    $resp = $list->execute(new SearchRequest(limit: 10, search: '', sortField: 'document', sort: 'desc'));

    expect((string) $resp->items[0]->document)->toBe('67887286077');
});
