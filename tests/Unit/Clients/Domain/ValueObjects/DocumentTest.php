<?php

declare(strict_types=1);

namespace Tests\Unit\Clients\Domain\ValueObjects;

use AppOficina\Clients\ValueObjects\Document;
use InvalidArgumentException;

// --- CPF Tests ---
it('should be a valid CPF', function (string $cpf) {
    $document = new Document($cpf);
    $document->validate();
    expect((string) $document)->toBe(preg_replace('/\D/', '', $cpf));
})->with([
    '42603972065',
    '678.872.860-77',
]);

it('should throw an exception for an invalid CPF', function (string $cpf) {
    $document = new Document($cpf);
    $document->validate();
})->with([
    '11111111111', // All digits are the same
    '12345678901', // Invalid verifier digit
    '123456789',   // Invalid length
])->throws(InvalidArgumentException::class);


// --- CNPJ Tests ---
it('should be a valid CNPJ', function (string $cnpj) {
    $document = new Document($cnpj);
    $document->validate();
    expect((string) $document)->toBe(preg_replace('/\D/', '', $cnpj));
})->with([
    '20.825.707/0001-44',
    '64361235000191',
]);

it('should throw an exception for an invalid CNPJ', function (string $cnpj) {
    $document = new Document($cnpj);
    $document->validate();
})->with([
    '11111111111111', // All digits are the same
    '12345678901234', // Invalid verifier digit
    '123456789012',   // Invalid length
])->throws(InvalidArgumentException::class);

// --- General Tests ---
it('should throw an exception for a document with invalid length', function () {
    $document = new Document('123');
    $document->validate();
})->throws(InvalidArgumentException::class, 'Document must be a valid CPF or CNPJ.');

it('should return the correct document type', function () {
    $cpf = new Document('12345678909');
    $cnpj = new Document('12345678901234');

    expect($cpf->getType())->toBe('CPF')
        ->and($cnpj->getType())->toBe('CNPJ');
});
