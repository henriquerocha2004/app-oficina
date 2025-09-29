<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\Domain\ValueObjects;

use AppOficina\Clients\ValueObjects\Address;
use InvalidArgumentException;

it('creates an address with valid fields', function () {
    $address = new Address('Main St', 'Anytown', 'CA', '12345');
    expect($address)->toBeInstanceOf(Address::class)
        ->and($address->street)->toBe('Main St');
});

it('throws on empty address fields', function () {
    new Address(' ', ' ', ' ', ' ');
})->throws(InvalidArgumentException::class);
