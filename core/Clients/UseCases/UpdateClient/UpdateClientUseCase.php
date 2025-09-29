<?php

declare(strict_types=1);

namespace AppOficina\Clients\UseCases\UpdateClient;

use AppOficina\Clients\Exceptions\ClientNotFoundException;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Clients\UseCases\UpdateClient\Input;
use AppOficina\Clients\ValueObjects\Address;
use AppOficina\Clients\ValueObjects\Phone;
use AppOficina\Shared\Exceptions\NotFoundException;
use Symfony\Component\Uid\Ulid;

class UpdateClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @throws NotFoundException
     */
    public function execute(Input $input): void
    {
        /** @var Client $client */
        $client = $this->repository->findById(Ulid::fromString($input->clientId));

        if (!$client) {
            throw new ClientNotFoundException();
        }

        $updatedClient = $client;
        if (isset($input->name)) {
            $updatedClient = $updatedClient->withName($input->name);
        }
        if (isset($input->email)) {
            $updatedClient = $updatedClient->withEmail($input->email);
        }
        if (isset($input->address)) {
            $addressData = $input->address;
            $updatedClient = $updatedClient->withAddress(new Address(
                street: $addressData['street'],
                city: $addressData['city'],
                state: $addressData['state'],
                zipCode: $addressData['zip_code'],
            ));
        }
        if (isset($input->phone)) {
            $updatedClient = $updatedClient->withPhone(new Phone($input->phone));
        }
        if (isset($input->observations)) {
            $updatedClient = $updatedClient->withObservations($input->observations);
        }

        $this->repository->update($updatedClient);
    }
}
