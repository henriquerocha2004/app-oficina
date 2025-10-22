<?php

namespace AppOficina\Clients\UseCases\CreateClient;

use AppOficina\Clients\ValueObjects\Phone;
use AppOficina\Clients\ValueObjects\Address;
use AppOficina\Clients\Entities\Client;
use AppOficina\Clients\Repository\ClientRepositoryInterface;

class CreateClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $repository
    ) {
    }

    public function execute(Input $data): Output
    {
        $client = $this->repository->findByDocument($data->document);

        if ($client !== null) {
            return new Output(clientId: (string) $client->id);
        }

        $client = Client::create(
            name: $data->name,
            email: $data->email,
            documentNumber: $data->document,
        );

        if ($data->address !== null) {
            $address = new Address(
                street: $data->address['street'],
                city: $data->address['city'],
                state: $data->address['state'],
                zipCode: $data->address['zipCode'],
            );

            $client = $client->withAddress($address);
        }

        if ($data->phone !== null) {
            $phone = new Phone($data->phone);
            $client = $client->withPhone($phone);
        }

        if ($data->observations !== null) {
            $client = $client->withObservations($data->observations);
        }

        $this->repository->save($client);

        return new Output(clientId: (string) $client->id);
    }
}
