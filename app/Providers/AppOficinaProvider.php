<?php

namespace App\Providers;

use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Infra\Repository\Clients\ClientEloquentRepository;
use AppOficina\Infra\Repository\Vehicles\VehicleEloquentRepository;
use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppOficinaProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings only - mappers are resolved directly via app()
        $this->app->bind(ClientRepositoryInterface::class, ClientEloquentRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleEloquentRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
