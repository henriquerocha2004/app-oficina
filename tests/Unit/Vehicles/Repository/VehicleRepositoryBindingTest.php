<?php

declare(strict_types=1);

namespace Tests\Unit\Vehicles\Repository;

use AppOficina\Vehicles\Repository\VehicleRepositoryInterface;
use AppOficina\Infra\Repository\Vehicles\VehicleEloquentMapper;
use AppOficina\Infra\Repository\Vehicles\VehicleEloquentRepository;
use AppOficina\Shared\Mapper\MapperInterface;
use Tests\TestCase;

class VehicleRepositoryBindingTest extends TestCase
{
    public function testCanResolveVehicleRepositoryFromContainer(): void
    {
        // Test direct instantiation first
        $directRepo = new VehicleEloquentRepository();
        $this->assertInstanceOf(VehicleEloquentRepository::class, $directRepo);

        // Test container resolution
        $repository = $this->app->make(VehicleRepositoryInterface::class);
        $this->assertInstanceOf(VehicleEloquentRepository::class, $repository);
    }

    public function testCanCreateVehicleMapperDirectly(): void
    {
        // Test direct instantiation (no binding needed)
        $mapper = app(VehicleEloquentMapper::class);

        $this->assertInstanceOf(VehicleEloquentMapper::class, $mapper);
        $this->assertInstanceOf(MapperInterface::class, $mapper);
    }
}
