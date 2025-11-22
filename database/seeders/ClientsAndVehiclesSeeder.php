<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsAndVehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to seed clients and cars...');
        // Verificar se já existem dados
        if (Client::count() > 0) {
            $this->command->warn('⚠️  Database already contains clients. Truncating tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Vehicle::truncate();
            Client::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        DB::transaction(function () {
            // Criar 100 clientes
            $clients = Client::factory()->count(100)->create();

            $this->command->info("Created {$clients->count()} clients");

            // Para cada cliente, criar um carro
            $clients->each(function (Client $client, $index) {
                // Varia os tipos de carros para ter diversidade
                $carTypes = ['motorcycle', 'motorcycle'];

                // Criar carro com características variadas
                $carFactory = Vehicle::factory()->forClient($client->id);

                // Adicionar variação baseada no índice para ter diversidade
                switch ($index % 8) {
                    case 0:
                        $car = $carFactory->recent()->automatic()->ofType('car')->create();
                        break;
                    case 1:
                        $car = $carFactory->vintage()->manual()->ofType('motorcycle')->create();
                        break;
                    default:
                        $car = $carFactory->ofType($carTypes[array_rand($carTypes)])->create();
                        break;
                }

                if ($index % 10 === 0) {
                    $this->command->info("Progress: " . ($index + 1) . "/100 clients with cars created");
                }
            });
        });

        $this->command->info('✅ Successfully created 100 clients, each with one car!');
        $this->displaySummary();
    }

    /**
     * Display a summary of created data
     */
    private function displaySummary(): void
    {
        $clientsCount = Client::count();
        $carsCount = Vehicle::count();

        $carsByType = Vehicle::select('vehicle_type', DB::raw('count(*) as total'))
            ->groupBy('vehicle_type')
            ->pluck('total', 'vehicle_type')
            ->toArray();

        $carsByTransmission = Vehicle::select('transmission', DB::raw('count(*) as total'))
            ->whereNotNull('transmission')
            ->groupBy('transmission')
            ->pluck('total', 'transmission')
            ->toArray();

        $this->command->info('📊 Summary:');
        $this->command->info("   Total Clients: {$clientsCount}");
        $this->command->info("   Total Vehicles: {$carsCount}");

        $this->command->info('🚗 Vehicles by Type:');
        foreach ($carsByType as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        $this->command->info('⚙️ Vehicles by Transmission:');
        foreach ($carsByTransmission as $transmission => $count) {
            $this->command->info("   {$transmission}: {$count}");
        }

        $carsWithPlates = Vehicle::whereNotNull('license_plate')->count();
        $carsWithVin = Vehicle::whereNotNull('vin')->count();

        $this->command->info('📋 Additional Stats:');
        $this->command->info("   Vehicles with License Plates: {$carsWithPlates}");
        $this->command->info("   Vehicles with VIN: {$carsWithVin}");
    }
}
