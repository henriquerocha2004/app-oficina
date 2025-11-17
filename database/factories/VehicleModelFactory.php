<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Uid\Ulid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Ulid::generate(),
            'client_id' => Ulid::generate(),
            'brand' => $this->faker->randomElement([
                'Toyota',
                'Honda',
                'Ford',
                'Chevrolet',
                'Volkswagen',
                'Hyundai',
                'Nissan',
                'BMW',
                'Mercedes-Benz',
                'Audi',
                'Fiat',
                'Renault',
                'Peugeot',
                'Citroën',
                'Jeep'
            ]),
            'model' => $this->faker->randomElement([
                'Corolla',
                'Civic',
                'Focus',
                'Cruze',
                'Golf',
                'Elantra',
                'Sentra',
                'Serie 3',
                'Classe C',
                'A3',
                'Uno',
                'Sandero',
                '208',
                'C3',
                'Compass'
            ]),
            'year' => $this->faker->numberBetween(1886, (int)date('Y')),
            'vehicle_type' => $this->faker->randomElement([
                'sedan',
                'hatchback',
                'suv',
                'coupe',
                'convertible',
                'wagon',
                'van',
                'pickup'
            ]),
            'color' => $this->faker->optional(0.8)->randomElement([
                'Branco',
                'Preto',
                'Prata',
                'Azul',
                'Vermelho',
                'Cinza',
                'Verde',
                'Amarelo',
                'Dourado',
                'Bronze'
            ]),
            'cilinder_capacity' => $this->faker->optional(0.7)->numberBetween(1000, 6000),
            'mileage' => $this->faker->optional(0.8)->numberBetween(0, 300000),
            'observations' => $this->faker->optional(0.3)->sentence(),
            'license_plate' => $this->generateBrazilianPlate(),
            'vin' => $this->generateValidVin(),
            'transmission' => $this->faker->optional(0.9)->randomElement(['manual', 'automatic']),
        ];
    }

    /**
     * Generate a valid Brazilian license plate (old or Mercosul format)
     */
    private function generateBrazilianPlate(): ?string
    {
        if ($this->faker->boolean(20)) { // 20% chance of null
            return null;
        }

        $isMercosul = $this->faker->boolean(30); // 30% chance of Mercosul format

        if ($isMercosul) {
            // Mercosul format: ABC1D23
            return sprintf(
                '%s%s%s%d%s%d%d',
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->numberBetween(0, 9),
                $this->faker->randomLetter(),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9)
            );
        } else {
            // Old format: ABC1234
            return sprintf(
                '%s%s%s%d%d%d%d',
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9)
            );
        }
    }

    /**
     * Generate a valid VIN number (17 characters, no I, O, Q)
     */
    private function generateValidVin(): ?string
    {
        if ($this->faker->boolean(15)) { // 15% chance of null
            return null;
        }

        $validChars = 'ABCDEFGHJKLMNPRSTUVWXYZ0123456789'; // No I, O, Q
        $vin = '';

        for ($i = 0; $i < 17; $i++) {
            $vin .= $validChars[random_int(0, strlen($validChars) - 1)];
        }

        return $vin;
    }

    /**
     * Create a car with all optional fields filled
     */
    public function complete(): static
    {
        return $this->state(fn(array $attributes) => [
            'color' => $this->faker->randomElement([
                'Branco',
                'Preto',
                'Prata',
                'Azul',
                'Vermelho',
                'Cinza',
                'Verde',
                'Amarelo',
                'Dourado',
                'Bronze'
            ]),
            'cilinder_capacity' => $this->faker->numberBetween(1000, 6000),
            'mileage' => $this->faker->numberBetween(0, 300000),
            'observations' => $this->faker->sentence(),
            'license_plate' => $this->generateBrazilianPlate() ?? $this->generateBrazilianPlate(),
            'vin' => $this->generateValidVin() ?? $this->generateValidVin(),
            'transmission' => $this->faker->randomElement(['manual', 'automatic']),
        ]);
    }

    /**
     * Create a car with only required fields
     */
    public function minimal(): static
    {
        return $this->state(fn(array $attributes) => [
            'color' => '',
            'cilinder_capacity' => 0,
            'mileage' => 0,
            'observations' => '',
            'license_plate' => null,
            'vin' => null,
            'transmission' => null,
        ]);
    }

    /**
     * Create a car with specific type
     */
    public function ofType(string $type): static
    {
        return $this->state(fn(array $attributes) => [
            'vehicle_type' => $type,
        ]);
    }

    /**
     * Create a car with specific brand and model
     */
    public function brandModel(string $brand, string $model): static
    {
        return $this->state(fn(array $attributes) => [
            'brand' => $brand,
            'model' => $model,
        ]);
    }

    /**
     * Create a vintage car (older than 25 years)
     */
    public function vintage(): static
    {
        return $this->state(fn(array $attributes) => [
            'year' => $this->faker->numberBetween(1886, (int)date('Y') - 25),
        ]);
    }

    /**
     * Create a recent car (less than 3 years old)
     */
    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'year' => $this->faker->numberBetween((int)date('Y') - 3, (int)date('Y')),
            'mileage' => $this->faker->numberBetween(0, 50000),
        ]);
    }

    /**
     * Create a car with valid old format license plate (ABC1234)
     */
    public function withOldFormatPlate(): static
    {
        return $this->state(fn(array $attributes) => [
            'license_plate' => sprintf(
                '%s%s%s%d%d%d%d',
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9)
            ),
        ]);
    }

    /**
     * Create a car with valid Mercosul format license plate (ABC1D23)
     */
    public function withMercosulPlate(): static
    {
        return $this->state(fn(array $attributes) => [
            'license_plate' => sprintf(
                '%s%s%s%d%s%d%d',
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->randomLetter(),
                $this->faker->numberBetween(0, 9),
                $this->faker->randomLetter(),
                $this->faker->numberBetween(0, 9),
                $this->faker->numberBetween(0, 9)
            ),
        ]);
    }

    /**
     * Create a car with manual transmission
     */
    public function manual(): static
    {
        return $this->state(fn(array $attributes) => [
            'transmission' => 'manual',
        ]);
    }

    /**
     * Create a car with automatic transmission
     */
    public function automatic(): static
    {
        return $this->state(fn(array $attributes) => [
            'transmission' => 'automatic',
        ]);
    }

    /**
     * Create a car with specific year (validates domain rules)
     */
    public function fromYear(int $year): static
    {
        $currentYear = (int)date('Y');
        $minYear = 1886; // First car invention year as per domain rules

        if ($year < $minYear || $year > $currentYear) {
            throw new \InvalidArgumentException("Year must be between {$minYear} and {$currentYear}");
        }

        return $this->state(fn(array $attributes) => [
            'year' => $year,
        ]);
    }

    /**
     * Create a car with minimum brand length (3 characters as per domain rules)
     */
    public function withMinimumBrand(): static
    {
        return $this->state(fn(array $attributes) => [
            'brand' => 'BMW', // Exactly 3 characters
        ]);
    }

    /**
     * Create a car with specific client ID
     */
    public function forClient(string $clientId): static
    {
        return $this->state(fn(array $attributes) => [
            'client_id' => $clientId,
        ]);
    }
}
