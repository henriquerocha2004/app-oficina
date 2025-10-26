<?php

declare(strict_types=1);

namespace AppOficina\Clients\ValueObjects;

use InvalidArgumentException;

final class Document
{
    private string $number;

    public function __construct(string $number)
    {
        $this->number = preg_replace('/\D/', '', $number);
    }

    public function validate(): void
    {
        if (strlen($this->number) === 11) {
            if (!$this->isValidCpf()) {
                throw new InvalidArgumentException('Invalid CPF number.');
            }
            return;
        }

        if (strlen($this->number) === 14) {
            if (!$this->isValidCnpj()) {
                throw new InvalidArgumentException('Invalid CNPJ number.');
            }
            return;
        }

        throw new InvalidArgumentException('Document must be a valid CPF or CNPJ.');
    }

    public function getType(): string
    {
        if (strlen($this->number) === 11) {
            return 'CPF';
        }

        if (strlen($this->number) === 14) {
            return 'CNPJ';
        }

        return 'Unknown';
    }

    public function __toString(): string
    {
        return $this->number;
    }

    private function isValidCpf(): bool
    {
        if (preg_match('/(\d)\1{10}/', $this->number)) {
            return false;
        }

        $baseDigits = substr($this->number, 0, 9);
        $firstVerifier = $this->calculateCpfVerifierDigit($baseDigits, 10);
        $secondVerifier = $this->calculateCpfVerifierDigit($baseDigits . $firstVerifier, 11);

        return $this->number[9] == $firstVerifier && $this->number[10] == $secondVerifier;
    }

    private function calculateCpfVerifierDigit(string $base, int $weight): int
    {
        $sum = 0;
        for ($i = 0; $i < strlen($base); $i++) {
            $sum += (int)$base[$i] * $weight--;
        }
        $remainder = $sum % 11;
        return $remainder < 2 ? 0 : 11 - $remainder;
    }

    private function isValidCnpj(): bool
    {
        if (preg_match('/(\d)\1{13}/', $this->number)) {
            return false;
        }

        $baseDigits = substr($this->number, 0, 12);
        $firstVerifier = $this->calculateCnpjVerifierDigit($baseDigits, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $secondVerifier = $this->calculateCnpjVerifierDigit($baseDigits . $firstVerifier, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        return $this->number[12] == $firstVerifier && $this->number[13] == $secondVerifier;
    }

    private function calculateCnpjVerifierDigit(string $base, array $weights): int
    {
        $sum = 0;
        for ($i = 0; $i < strlen($base); $i++) {
            $sum += (int)$base[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
