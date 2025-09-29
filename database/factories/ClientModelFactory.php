<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientModelFactory extends Factory
{
    public function definition(): array
    {
        $faker = $this->withFaker('pt_BR');

        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'document_number' => $faker->cpf(false),
            'street' => $faker->streetAddress(),
            'city' => $faker->city(),
            'state' => $faker->state(),
            'zip_code' => $faker->postcode(),
            'phone' => $faker->phoneNumber(),
            'observations' => $faker->text(),
        ];
    }
}
