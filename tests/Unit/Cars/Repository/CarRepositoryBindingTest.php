<?php

declare(strict_types=1);

namespace Tests\Unit\Cars\Repository;

use AppOficina\Cars\Repository\CarRepositoryInterface;
use AppOficina\Infra\Repository\Cars\CarEloquentMapper;
use AppOficina\Infra\Repository\Cars\CarEloquentRepository;
use AppOficina\Shared\Mapper\MapperInterface;
use Tests\TestCase;

class CarRepositoryBindingTest extends TestCase
{
    public function testCanResolveCarRepositoryFromContainer(): void
    {
        // Test direct instantiation first
        $directRepo = new CarEloquentRepository();
        $this->assertInstanceOf(CarEloquentRepository::class, $directRepo);

        // Test container resolution
        $repository = $this->app->make(CarRepositoryInterface::class);
        $this->assertInstanceOf(CarEloquentRepository::class, $repository);
    }

    public function testCanCreateCarMapperDirectly(): void
    {
        // Test direct instantiation (no binding needed)
        $mapper = app(CarEloquentMapper::class);

        $this->assertInstanceOf(CarEloquentMapper::class, $mapper);
        $this->assertInstanceOf(MapperInterface::class, $mapper);
    }
}
