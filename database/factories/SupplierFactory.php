<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Ltda',
            'trade_name' => $this->faker->optional()->companySuffix(),
            'document_number' => $this->generateCNPJ(),
            'email' => $this->faker->optional()->companyEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'mobile' => $this->faker->optional()->phoneNumber(),
            'website' => $this->faker->optional()->url(),
            'street' => $this->faker->optional()->streetName(),
            'number' => $this->faker->optional()->buildingNumber(),
            'complement' => $this->faker->optional()->secondaryAddress(),
            'neighborhood' => $this->faker->optional()->citySuffix(),
            'city' => $this->faker->optional()->city(),
            'state' => $this->faker->optional()->stateAbbr(),
            'zip_code' => $this->faker->optional()->postcode(),
            'contact_person' => $this->faker->optional()->name(),
            'payment_term_days' => $this->faker->optional()->randomElement([30, 60, 90]),
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(95),
        ];
    }

    /**
     * Generate a valid CNPJ (simplified for testing)
     */
    private function generateCNPJ(): string
    {
        // Generate 14 random digits for testing purposes
        return sprintf(
            '%02d%03d%03d%04d%02d',
            $this->faker->numberBetween(10, 99),
            $this->faker->numberBetween(100, 999),
            $this->faker->numberBetween(100, 999),
            $this->faker->numberBetween(1000, 9999),
            $this->faker->numberBetween(10, 99)
        );
    }

    /**
     * Indicate that the supplier is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
