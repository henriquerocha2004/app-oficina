<?php

namespace AppOficina\Cars\Enums;

enum TransmissionType: string
{
    case MANUAL = 'manual';
    case AUTOMATIC = 'automatic';

    public function label(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::AUTOMATIC => 'Automatic',
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
