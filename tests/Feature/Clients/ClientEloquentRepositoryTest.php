<?php

use AppOficina\Infra\Repository\Clients\ClientEloquentRepository;
use AppOficina\Clients\Entities\Client;
use AppOficina\Clients\ValueObjects\Address;
use AppOficina\Clients\ValueObjects\Phone;
use AppOficina\Shared\Search\SearchRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
it('saves and finds a client by id and document', function () {
    $repo = app(ClientEloquentRepository::class);

    $client = Client::create('Alice', 'alice@example.com', '42603972065');
    $client = $client->withAddress(new Address('Street 1', 'City', 'ST', '12345'));
    $client = $client->withPhone(new Phone('11999999999'));

    $repo->save($client);

    /** @var Client $found */
    $found = $repo->findById($client->id);
    expect($found)->not->toBeNull()
        ->and($found->name)->toBe('Alice')
        ->and($found->email)->toBe('alice@example.com')
        ->and((string) $found->document)->toBe('42603972065');

    $foundByDoc = $repo->findByDocument('42603972065');
    expect($foundByDoc)->not->toBeNull()
        ->and($foundByDoc->email)->toBe('alice@example.com');
});

it('updates client fields and persists changes', function () {
    $repo = app(ClientEloquentRepository::class);

    $client = Client::create('Bob', 'bob@example.com', '67887286077');
    $repo->save($client);

    $updated = $client->withName('Bobby')->withEmail('bobby@example.com')->withObservations('Updated');
    $repo->update($updated);

     /** @var Client $found */
    $found = $repo->findById($client->id);
    expect($found)->not->toBeNull()
        ->and($found->name)->toBe('Bobby')
        ->and($found->email)->toBe('bobby@example.com')
        ->and($found->observations)->toBe('Updated');
});

it('deletes client (soft delete) and it is no longer found', function () {
    $repo = app(ClientEloquentRepository::class);

    $client = Client::create('Carol', 'carol@example.com', '15540258002');
    $repo->save($client);

    $repo->delete($client->id);

    $found = $repo->findById($client->id);
    expect($found)->toBeNull();
});

it('lists clients via search request', function () {
    $repo = app(ClientEloquentRepository::class);

    $clients = [
        Client::create('A', 'a@example.com', '42603972065'),
        Client::create('B', 'b@example.com', '67887286077'),
        Client::create('C', 'c@example.com', '64361235000191'),
    ];

    foreach ($clients as $c) {
        $repo->save($c);
    }

    $resp = $repo->findAll(new SearchRequest(limit: 10, search: '', sortField: 'name', sort: 'asc', page: 1));
    expect($resp->totalItems)->toBe(3)
        ->and(count($resp->items))->toBe(3)
        ->and($resp->items[0]['name'])->toBe('A');
});

it('sorts clients by document_number desc and asc', function () {
    $repo = app(ClientEloquentRepository::class);

    $clients = [
        Client::create('A', 'a@example.com', '42603972065'),
        Client::create('B', 'b@example.com', '67887286077'),
        Client::create('C', 'c@example.com', '15540258002'),
    ];

    foreach ($clients as $c) {
        $repo->save($c);
    }

    $desc = $repo->findAll(new SearchRequest(limit: 10, search: '', sortField: 'document_number', sort: 'desc', page: 1));
    expect($desc->items[0]['document'])->toBe('67887286077')
        ->and($desc->items[1]['document'])->toBe('42603972065');

    $asc = $repo->findAll(new SearchRequest(limit: 10, search: '', sortField: 'document_number', sort: 'asc', page: 1));
    expect($asc->items[0]['document'])->toBe('15540258002')
        ->and($asc->items[2]['document'])->toBe('67887286077');
});

it('filters by column search on email and street', function () {
    $repo = app(ClientEloquentRepository::class);

    $c1 = Client::create('Alpha', 'alpha@example.com', '42603972065');
    $c1 = $c1->withAddress(new Address('Main St', 'City', 'ST', '11111'));
    $repo->save($c1);

    $c2 = Client::create('Beta', 'beta@example.com', '67887286077');
    $c2 = $c2->withAddress(new Address('Second St', 'City', 'ST', '22222'));
    $repo->save($c2);

    // columnSearch by email
    $respEmail = $repo->findAll(new SearchRequest(limit: 10, search: '', sortField: 'name', sort: 'asc', page: 1, columnSearch: [['field' => 'email', 'value' => 'beta@example.com']]));
    expect($respEmail->totalItems)->toBe(1)
        ->and($respEmail->items[0]['email'])->toBe('beta@example.com');

    // columnSearch by street
    $respStreet = $repo->findAll(new SearchRequest(limit: 10, search: '', sortField: 'name', sort: 'asc', page: 1, columnSearch: [['field' => 'street', 'value' => 'Main St']]));
    expect($respStreet->totalItems)->toBe(1)
        ->and($respStreet->items[0]['address']['street'])->toBe('Main St');
});

it('paginates results with edge page/limit values', function () {
    $repo = app(ClientEloquentRepository::class);

    $docs = ['42603972065', '67887286077', '15540258002', '64361235000191', '20825707000144'];
    for ($i = 1; $i <= 5; $i++) {
        $repo->save(Client::create('User' . $i, "user{$i}@example.com", $docs[$i - 1]));
    }

    // page 2 with limit 2 should return 2 items (3rd and 4th)
    $resp = $repo->findAll(new SearchRequest(limit: 2, search: '', sortField: 'name', sort: 'asc', page: 2));
    expect($resp->totalItems)->toBe(5)
        ->and(count($resp->items))->toBe(2)
        ->and($resp->items[0]['name'])->toBe('User3');
});
