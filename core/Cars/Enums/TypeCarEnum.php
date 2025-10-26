<?php

namespace AppOficina\Cars\Enums;

enum TypeCarEnum: string
{
    case SEDAN = 'sedan';
    case HATCHBACK = 'hatchback';
    case SUV = 'suv';
    case COUPE = 'coupe';
    case CONVERTIBLE = 'convertible';
    case WAGON = 'wagon';
    case VAN = 'van';
    case PICKUP = 'pickup';

    public function label(): string
    {
        return match ($this) {
            self::SEDAN => 'Sedan',
            self::HATCHBACK => 'Hatchback',
            self::SUV => 'SUV',
            self::COUPE => 'Coupe',
            self::CONVERTIBLE => 'Convertible',
            self::WAGON => 'Wagon',
            self::VAN => 'Van',
            self::PICKUP => 'Pickup',
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
        ];
    }

    public static function all(): array
    {
        return array_map(fn (self $type) => $type->toArray(), self::cases());
    }
}
