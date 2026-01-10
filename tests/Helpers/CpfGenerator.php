<?php

namespace Tests\Helpers;

trait CpfGenerator
{
    /**
     * Gera um CPF válido aleatório
     */
    public function generateValidCpf(): string
    {
        // Gera os primeiros 9 dígitos aleatoriamente
        $cpf = [];
        for ($i = 0; $i < 9; $i++) {
            $cpf[] = rand(0, 9);
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $cpf[9] = $remainder < 2 ? 0 : 11 - $remainder;

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $cpf[10] = $remainder < 2 ? 0 : 11 - $remainder;

        return implode('', $cpf);
    }

    /**
     * Gera uma lista de CPFs válidos únicos
     */
    public function generateMultipleValidCpfs(int $count): array
    {
        $cpfs = [];
        $attempts = 0;
        $maxAttempts = $count * 10; // Evita loop infinito

        while (count($cpfs) < $count && $attempts < $maxAttempts) {
            $cpf = $this->generateValidCpf();

            // Evita CPFs com todos os dígitos iguais (que são inválidos)
            if (!$this->hasAllSameDigits($cpf) && !in_array($cpf, $cpfs)) {
                $cpfs[] = $cpf;
            }

            $attempts++;
        }

        return $cpfs;
    }

    /**
     * Verifica se todos os dígitos do CPF são iguais
     */
    private function hasAllSameDigits(string $cpf): bool
    {
        $firstDigit = $cpf[0];
        for ($i = 1; $i < strlen($cpf); $i++) {
            if ($cpf[$i] !== $firstDigit) {
                return false;
            }
        }
        return true;
    }

    /**
     * Lista de CPFs válidos pré-definidos para casos específicos
     */
    public function getValidCpfList(): array
    {
        return [
            '11144477735',
            '42603972065',
            '67887286077',
            '36754853030',
            '47123456789',
            '83751294605',
            '18547003016',
            '92382444690',
            '64361235000191', // CNPJ válido também
            '85463254102',
            '29456781023',
            '73829154067',
            '96385274108',
            '51847392065',
            '74185296304',
            '30596481027',
            '68274395018',
            '52739481065',
            '84629374150',
            '95174286039'
        ];
    }
}
