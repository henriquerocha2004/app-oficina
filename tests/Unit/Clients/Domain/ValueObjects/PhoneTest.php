<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\Domain\ValueObjects;

use AppOficina\Clients\ValueObjects\Phone;
use InvalidArgumentException;

it('accepts valid phone numbers', function () {
    $phone = new Phone('11987654321');
    expect($phone)->toBeInstanceOf(Phone::class)
        ->and((string) $phone)->toBe('11987654321');
});

it('throws for invalid phone numbers', function () {
    new Phone('abc');
})->throws(InvalidArgumentException::class);
