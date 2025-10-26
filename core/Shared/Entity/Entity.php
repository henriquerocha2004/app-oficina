<?php

declare(strict_types=1);

namespace AppOficina\Shared\Entity;

use Symfony\Component\Uid\Ulid;

abstract class Entity
{
    public Ulid $id;

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function toArray(): array
    {
        $result = [];

        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties() as $prop) {
            $prop->setAccessible(true);
            $name = $prop->getName();
            $snake = $this->toSnakeCase($name);
            $value = $prop->getValue($this);
            $result[$snake] = $this->normalizeValue($value);
        }

        return $result;
    }

    private function toSnakeCase(string $input): string
    {
        $snake = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $input);
        $snake = preg_replace('/([A-Z])([A-Z][a-z])/', '$1_$2', $snake);
        $snake = strtolower($snake);
        return ltrim($snake, '_');
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn($v) => $this->normalizeValue($v), $value);
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTime::ATOM);
        }

        if (is_object($value)) {
            if (method_exists($value, 'toArray')) {
                return $value->toArray();
            }

            if ($value instanceof \JsonSerializable) {
                return $value->jsonSerialize();
            }

            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            $objResult = [];
            $ref = new \ReflectionObject($value);
            foreach ($ref->getProperties() as $p) {
                $p->setAccessible(true);
                $objResult[$this->toSnakeCase($p->getName())] = $this->normalizeValue($p->getValue($value));
            }

            return $objResult;
        }

        return $value;
    }
}
