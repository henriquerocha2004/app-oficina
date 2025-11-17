<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\Domain\ValueObjects;

use AppOficina\Vehicles\Exceptions\InvalidChassiException;
use AppOficina\Vehicles\ValueObjects\Vin;

// --- Valid VIN Tests ---
it('should accept valid VIN numbers', function (string $vin) {
    $vinObject = new Vin($vin);
    expect($vinObject)->toBeInstanceOf(Vin::class)
        ->and($vinObject->value())->toBe($vin);
})->with([
    '1HGBH41JXMN109186', // Honda VIN
    'WBA3B1C50DF123456', // BMW VIN
    'JH4TB2H26CC000000', // Acura VIN
    '1FTFW1ET5DFC10312', // Ford VIN
    'KNDJN2A2XF7123456', // Kia VIN
    'WVWZZZ1JZ3W386752', // Volkswagen VIN
    '1G1ZT51816F109149', // Chevrolet VIN
    'JM1BL1SF7A1123456', // Mazda VIN
]);

it('should return the correct VIN value', function () {
    $vin = '1HGBH41JXMN109186';
    $vinObject = new Vin($vin);
    expect($vinObject->value())->toBe($vin);
});

// --- Invalid VIN Tests ---
it('should throw exception for invalid VIN numbers', function (string $vin) {
    new Vin($vin);
})->with([
    '1HGBH41JXMN10918',   // Too short (16 characters)
    '1HGBH41JXMN1091866', // Too long (18 characters)
    '1HGBH41JXMN10918I',  // Contains invalid character 'I'
    '1HGBH41JXMN10918O',  // Contains invalid character 'O'
    '1HGBH41JXMN10918Q',  // Contains invalid character 'Q'
    'IHGBH41JXMN109186',  // Contains invalid character 'I' at start
    'OHGBH41JXMN109186',  // Contains invalid character 'O' at start
    'QHGBH41JXMN109186',  // Contains invalid character 'Q' at start
    '1hgbh41jxmn109186',  // Lowercase letters
    '1HGBh41JXMN109186',  // Mixed case
    '123456789012345678', // Too long with only numbers
    'ABCDEFGHIJKLMNOPQ',  // Too short with only letters
    '',                   // Empty string
    '1HGB-41JXMN109186',  // Contains hyphen
    '1HGB 41JXMN109186',  // Contains space
    '1HGB_41JXMN109186',  // Contains underscore
])->throws(InvalidChassiException::class);

// --- Edge Cases ---
it('should accept VIN with all valid numbers', function () {
    $vin = '12345678901234567';
    $vinObject = new Vin($vin);
    expect($vinObject->value())->toBe($vin);
});

it('should accept VIN with all valid letters', function () {
    $vin = 'ABCDEFGHJKLMNPRST';
    $vinObject = new Vin($vin);
    expect($vinObject->value())->toBe($vin);
});

it('should accept VIN with mix of valid characters', function () {
    $vin = 'A1B2C3D4E5F6G7H8J';
    $vinObject = new Vin($vin);
    expect($vinObject->value())->toBe($vin);
});

// --- Equality Tests ---
it('should correctly compare equal VINs', function () {
    $vin1 = new Vin('1HGBH41JXMN109186');
    $vin2 = new Vin('1HGBH41JXMN109186');
    expect($vin1->equals($vin2))->toBeTrue();
});

it('should correctly compare different VINs', function () {
    $vin1 = new Vin('1HGBH41JXMN109186');
    $vin2 = new Vin('WBA3B1C50DF123456');
    expect($vin1->equals($vin2))->toBeFalse();
});

it('should correctly compare VINs with same content but different objects', function () {
    $vinString = '1HGBH41JXMN109186';
    $vin1 = new Vin($vinString);
    $vin2 = new Vin($vinString);
    expect($vin1->equals($vin2))->toBeTrue();
});

// --- Character Set Validation Tests ---
it('should reject VINs with forbidden characters I, O, Q', function (string $forbiddenChar) {
    $vin = '1HGBH41JXMN10918' . $forbiddenChar;
    new Vin($vin);
})->with(['I', 'O', 'Q'])->throws(InvalidChassiException::class);

it('should accept VINs with all allowed letters except I, O, Q', function () {
    $allowedLetters = [
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'J',
        'K',
        'L',
        'M',
        'N',
        'P',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z'
    ];

    foreach ($allowedLetters as $letter) {
        $vin = '1HGBH41JXMN10918' . $letter;
        $vinObject = new Vin($vin);
        expect($vinObject->value())->toBe($vin);
    }
});

it('should accept VINs with all allowed numbers 0-9', function () {
    for ($i = 0; $i <= 9; $i++) {
        $vin = '1HGBH41JXMN10918' . $i;
        $vinObject = new Vin($vin);
        expect($vinObject->value())->toBe($vin);
    }
});

// --- Boundary Tests ---
it('should accept exactly 17 characters', function () {
    $vin = str_repeat('A', 17);
    $vinObject = new Vin($vin);
    expect($vinObject->value())->toBe($vin);
});

it('should reject 16 characters', function () {
    $vin = str_repeat('A', 16);
    new Vin($vin);
})->throws(InvalidChassiException::class);

it('should reject 18 characters', function () {
    $vin = str_repeat('A', 18);
    new Vin($vin);
})->throws(InvalidChassiException::class);
