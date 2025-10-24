<?php

declare(strict_types=1);

namespace AppOficina\Clients\Entities;

use InvalidArgumentException;
use Symfony\Component\Uid\Ulid;
use AppOficina\Clients\ValueObjects\Phone;
use AppOficina\Clients\ValueObjects\Address;
use AppOficina\Clients\ValueObjects\Document;
use AppOficina\Shared\Entity\Entity;

final class Client extends Entity
{
    private function __construct(
        ?Ulid $id = null,
        public readonly string $name,
        public readonly string $email,
        public readonly Document $document,
        public readonly ?Address $address = null,
        public readonly ?Phone $phone = null,
        public readonly string $observations = ''
    ) {
        $this->id = $id ?? new Ulid();

        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Client name cannot be empty.');
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }
    }

    public static function create(string $name, string $email, string $documentNumber, ?Ulid $id = null): self
    {
        $document = new Document($documentNumber);
        $document->validate();

        return new self(
            name: $name,
            email: strtolower($email),
            document: $document,
            id: $id
        );
    }

    public function withName(string $name): self
    {
        return new self(
            name: $name,
            email: $this->email,
            document: $this->document,
            address: $this->address,
            phone: $this->phone,
            observations: $this->observations,
            id: $this->id
        );
    }

    public function withEmail(string $email): self
    {
        return new self(
            name: $this->name,
            email: strtolower($email),
            document: $this->document,
            address: $this->address,
            phone: $this->phone,
            observations: $this->observations,
            id: $this->id
        );
    }

    public function withAddress(?Address $address): self
    {
        return new self(
            name: $this->name,
            email: $this->email,
            document: $this->document,
            address: $address,
            phone: $this->phone,
            observations: $this->observations,
            id: $this->id
        );
    }

    public function withPhone(?Phone $phone): self
    {
        return new self(
            name: $this->name,
            email: $this->email,
            document: $this->document,
            address: $this->address,
            phone: $phone,
            observations: $this->observations,
            id: $this->id
        );
    }

    public function withObservations(string $observations): self
    {
        return new self(
            name: $this->name,
            email: $this->email,
            document: $this->document,
            address: $this->address,
            phone: $this->phone,
            observations: $observations,
            id: $this->id
        );
    }

    public function withDocument(string $documentNumber): self
    {
        $document = new Document($documentNumber);
        $document->validate();

        return new self(
            name: $this->name,
            email: $this->email,
            document: $document,
            address: $this->address,
            phone: $this->phone,
            observations: $this->observations,
            id: $this->id
        );
    }
}
