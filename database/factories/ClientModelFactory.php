<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Uid\Ulid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientModelFactory extends Factory
{
    public function definition(): array
    {
        $faker = $this->withFaker('pt_BR');

        return [
            'id' => Ulid::generate(),
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'document_number' => $faker->cpf(false),
            'street' => $faker->streetAddress(),
            'city' => $faker->city(),
            'state' => $faker->randomElement(['SP', 'RJ', 'MG', 'ES', 'RS', 'SC', 'PR', 'BA', 'PE', 'CE']),
            'zip_code' => str_replace('-', '', $faker->postcode()),
            'phone' => $faker->phoneNumber(),
            'observations' => $faker->text(),
        ];
    }
}
