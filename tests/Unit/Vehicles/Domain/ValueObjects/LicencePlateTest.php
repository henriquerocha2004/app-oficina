<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\Domain\ValueObjects;

use AppOficina\Vehicles\Exceptions\InvalidLicensePlateException;
use AppOficina\Vehicles\ValueObjects\LicencePlate;

// --- Old Format Tests ---
it('should accept valid old format license plates', function (string $plate) {
    $licencePlate = new LicencePlate($plate);
    expect($licencePlate)->toBeInstanceOf(LicencePlate::class)
        ->and($licencePlate->isValid())->toBeTrue();
})->with([
    'ABC1234',
    'XYZ9876',
    'DEF5432',
    'abc1234', // Should work with lowercase
    'ABC-1234', // Should work with hyphen
    'abc-1234', // Should work with lowercase and hyphen
]);

it('should validate old format correctly', function () {
    $licencePlate = new LicencePlate('ABC1234');
    expect($licencePlate->isValid())->toBeTrue();
});

it('should format old format plates correctly', function () {
    $licencePlate = new LicencePlate('ABC1234');
    expect($licencePlate->getFormattedPlate())->toBe('ABC-1234');
});

it('should format old format plates with existing hyphen correctly', function () {
    $licencePlate = new LicencePlate('ABC-1234');
    expect($licencePlate->getFormattedPlate())->toBe('ABC-1234');
});

// --- Mercosul Format Tests ---
it('should accept valid Mercosul format license plates', function (string $plate) {
    $licencePlate = new LicencePlate($plate);
    expect($licencePlate)->toBeInstanceOf(LicencePlate::class)
        ->and($licencePlate->isValid())->toBeTrue()
        ->and($licencePlate->isMercosulFormat())->toBeTrue();
})->with([
    'ABC1D23',
    'XYZ9A76',
    'DEF5B32',
    'abc1d23', // Should work with lowercase
]);

it('should validate Mercosul format correctly', function () {
    $licencePlate = new LicencePlate('ABC1D23');
    expect($licencePlate->isMercosulFormat())->toBeTrue();
});

it('should format Mercosul format plates correctly', function () {
    $licencePlate = new LicencePlate('ABC1D23');
    expect($licencePlate->getFormattedPlate())->toBe('ABC1D23');
});

// --- Invalid Format Tests ---
it('should throw exception for invalid license plates', function (string $plate) {
    new LicencePlate($plate);
})->with([
    'ABC12345', // Too many numbers for old format
    'AB1234',   // Too few letters
    'ABCD1234', // Too many letters
    'ABC123',   // Too few numbers for old format
    '1234ABC',  // Numbers before letters
    'ABC12D3',  // Invalid Mercosul format (number after letter)
    'ABC1DD3',  // Two letters in a row in Mercosul
    'ABC12D',   // Incomplete Mercosul format
    '123',      // Too short
    '',         // Empty string
    'ABC-12D3', // Invalid mixed format
])->throws(InvalidLicensePlateException::class);

// --- Format Detection Tests ---
it('should correctly identify old format plates', function () {
    $oldPlate = new LicencePlate('ABC1234');
    expect($oldPlate->isValid())->toBeTrue()
        ->and($oldPlate->isMercosulFormat())->toBeFalse();
});

it('should correctly identify Mercosul format plates', function () {
    $mercosulPlate = new LicencePlate('ABC1D23');
    expect($mercosulPlate->isValid())->toBeTrue()
        ->and($mercosulPlate->isMercosulFormat())->toBeTrue();
});

// --- Case Insensitive Tests ---
it('should handle lowercase input correctly', function () {
    $licencePlate = new LicencePlate('abc1234');
    expect($licencePlate->getFormattedPlate())->toBe('ABC-1234');
});

it('should handle mixed case input correctly', function () {
    $licencePlate = new LicencePlate('aBc1D23');
    expect($licencePlate->getFormattedPlate())->toBe('ABC1D23')
        ->and($licencePlate->isMercosulFormat())->toBeTrue();
});

// --- Formatting Tests ---
it('should remove hyphens during formatting', function () {
    $oldFormat = new LicencePlate('ABC-1234');
    expect($oldFormat->getFormattedPlate())->toBe('ABC-1234');
});

it('should handle plates with spaces', function () {
    $licencePlate = new LicencePlate('ABC 1234');
    expect($licencePlate->getFormattedPlate())->toBe('ABC-1234');
});

// --- Edge Cases ---
it('should handle minimum valid old format', function () {
    $licencePlate = new LicencePlate('AAA0000');
    expect($licencePlate->isValid())->toBeTrue()
        ->and($licencePlate->getFormattedPlate())->toBe('AAA-0000');
});

it('should handle maximum valid old format', function () {
    $licencePlate = new LicencePlate('ZZZ9999');
    expect($licencePlate->isValid())->toBeTrue()
        ->and($licencePlate->getFormattedPlate())->toBe('ZZZ-9999');
});

it('should handle minimum valid Mercosul format', function () {
    $licencePlate = new LicencePlate('AAA0A00');
    expect($licencePlate->isValid())->toBeTrue()
        ->and($licencePlate->isMercosulFormat())->toBeTrue()
        ->and($licencePlate->getFormattedPlate())->toBe('AAA0A00');
});

it('should handle maximum valid Mercosul format', function () {
    $licencePlate = new LicencePlate('ZZZ9Z99');
    expect($licencePlate->isValid())->toBeTrue()
        ->and($licencePlate->isMercosulFormat())->toBeTrue()
        ->and($licencePlate->getFormattedPlate())->toBe('ZZZ9Z99');
});
