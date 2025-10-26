<?php

namespace App\Providers;

use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Infra\Repository\Cars\CarEloquentRepository;
use AppOficina\Infra\Repository\Clients\ClientEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppOficinaProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings only - mappers are resolved directly via app()
        $this->app->bind(ClientRepositoryInterface::class, ClientEloquentRepository::class);
        $this->app->bind(CarRepositoryInterface::class, CarEloquentRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
