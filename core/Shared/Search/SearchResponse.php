<?php

namespace AppOficina\Shared\Search;

class SearchResponse
{
    public readonly int $totalItems;
    public readonly array $items;

    public function __construct(int $totalItems, array $items)
    {
        $this->totalItems = $totalItems;
        $this->items = $items;
    }

    public function toArray(): array
    {
        return [
            'total_items' => $this->totalItems,
            'items' => $this->items,
        ];
    }
}
