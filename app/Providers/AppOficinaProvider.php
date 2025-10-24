<?php

namespace App\Providers;

use AppOficina\Clients\Repository\ClientMapperRepositoryInterface;
use AppOficina\Clients\Repository\ClientRepositoryInterface;
use AppOficina\Infra\Repository\Clients\ClientEloquentMapper;
use AppOficina\Infra\Repository\Clients\ClientEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppOficinaProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->registerBindings();
    }

    private function registerBindings(): void
    {
        $this->clientBindings();
    }

    private function clientBindings(): void
    {
        $this->app->bind(ClientRepositoryInterface::class, ClientEloquentRepository::class);
        $this->app->bind(ClientMapperRepositoryInterface::class, ClientEloquentMapper::class);
    }
}
