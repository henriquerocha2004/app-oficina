<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\Domain\Entities;

use AppOficina\Clients\Entities\Client;
use AppOficina\Clients\ValueObjects\Address;
use AppOficina\Clients\ValueObjects\Phone;
use InvalidArgumentException;

it('should create a client with valid data', function () {
    $client = Client::create(
        name: 'John Doe',
        email: 'john.doe@example.com',
        documentNumber: '12345678909' // Valid CPF for structure
    );

    expect($client)->toBeInstanceOf(Client::class)
        ->and($client->name)->toBe('John Doe')
        ->and($client->email)->toBe('john.doe@example.com')
        ->and((string) $client->document)->toBe('12345678909');
});

it('should not create a client with an empty name', function () {
    Client::create(
        name: ' ',
        email: 'john.doe@example.com',
        documentNumber: '12345678909'
    );
})->throws(InvalidArgumentException::class, 'Client name cannot be empty.');

it('should not create a client with an invalid email', function () {
    Client::create(
        name: 'John Doe',
        email: 'invalid-email',
        documentNumber: '12345678909'
    );
})->throws(InvalidArgumentException::class, 'Invalid email address.');

it('should return a new instance when changing name', function () {
    $client1 = Client::create('John Doe', 'john@example.com', '12345678909');
    $client2 = $client1->withName('Jane Doe');

    expect($client2)->not->toBe($client1)
        ->and($client2->name)->toBe('Jane Doe')
        ->and($client1->name)->toBe('John Doe');
});

it('should return a new instance when changing email', function () {
    $client1 = Client::create('John Doe', 'john@example.com', '12345678909');
    $client2 = $client1->withEmail('jane@example.com');

    expect($client2)->not->toBe($client1)
        ->and($client2->email)->toBe('jane@example.com')
        ->and($client1->email)->toBe('john@example.com');
});

it('should return a new instance when updating address', function () {
    $client1 = Client::create('John Doe', 'john@example.com', '12345678909');
    $address = new Address('Main St', 'Anytown', 'CA', '12345');
    $client2 = $client1->withAddress($address);

    expect($client2)->not->toBe($client1)
        ->and($client2->address)->toBe($address)
        ->and($client1->address)->toBeNull();
});

it('should return a new instance when updating phone', function () {
    $client1 = Client::create('John Doe', 'john@example.com', '12345678909');
    $phone = new Phone('11987654321');
    $client2 = $client1->withPhone($phone);

    expect($client2)->not->toBe($client1)
        ->and($client2->phone)->toBe($phone)
        ->and($client1->phone)->toBeNull();
});

it('should return a new instance when changing document', function () {
    $client1 = Client::create('John Doe', 'john@example.com', '15540258002');
    $client2 = $client1->withDocument('42603972065');

    expect($client2)->not->toBe($client1)
        ->and((string) $client2->document)->toBe('42603972065')
        ->and((string) $client1->document)->toBe('15540258002');
});
