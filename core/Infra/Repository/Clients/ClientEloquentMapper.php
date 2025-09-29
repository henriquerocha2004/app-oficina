<?php

namespace AppOficina\Infra\Repository\Clients;

use AppOficina\Clients\Entities\Client;
use AppOficina\Clients\ValueObjects\Address;
use AppOficina\Clients\ValueObjects\Phone;
use AppOficina\Shared\Entity\Entity;
use AppOficina\Shared\Mapper\MapperInterface;
use Symfony\Component\Uid\Ulid;

class ClientEloquentMapper implements MapperInterface
{
    public function toDomain(object $persistenceModel): Entity 
    { 
        $client = Client::create(
            name: $persistenceModel->name,
            email: $persistenceModel->email,
            documentNumber: $persistenceModel->document_number,
            id: Ulid::fromString($persistenceModel->id)
        );

        // Only set address when persistence model has address data
        $hasAddress = !empty($persistenceModel->street)
            || !empty($persistenceModel->city)
            || !empty($persistenceModel->state)
            || !empty($persistenceModel->zip_code);

        if ($hasAddress) {
            $client = $client->withAddress(new Address(
                street: $persistenceModel->street ?? '',
                city: $persistenceModel->city ?? '',
                state: $persistenceModel->state ?? '',
                zipCode: $persistenceModel->zip_code ?? ''
            ));
        }

        if ($persistenceModel->phone) {
            $client = $client->withPhone(new Phone($persistenceModel->phone));
        }

        if ($persistenceModel->observations) {
            $client = $client->withObservations($persistenceModel->observations);
        }

        return $client;
    }

    /** @param Client $domainModel */
    public function toPersistence(Entity $domainModel): array
    {
        if (!$domainModel instanceof Client) {
            throw new \InvalidArgumentException('Invalid domain model type');
        }

        $street = null;
        $city = null;
        $state = null;
        $zip = null;

        if ($domainModel->address !== null) {
            $street = $domainModel->address->street ? (string) $domainModel->address->street : null;
            $city = $domainModel->address->city ? (string) $domainModel->address->city : null;
            $state = $domainModel->address->state ? (string) $domainModel->address->state : null;
            $zip = $domainModel->address->zipCode ? (string) $domainModel->address->zipCode : null;
        }

        return [
            'id' => $domainModel->id->toString(),
            'name' => $domainModel->name,
            'email' => $domainModel->email,
            'document_number' => (string) $domainModel->document,
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip,
            'phone' => $domainModel->phone ? (string) $domainModel->phone : null,
            'observations' => $domainModel->observations,
        ];
    }

}
