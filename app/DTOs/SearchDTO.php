<?php

namespace App\DTOs;

readonly class SearchDTO
{
    public function __construct(
        public ?string $search = null,
        public int $per_page = 15,
        public string $sort_by = 'created_at',
        public string $sort_direction = 'desc',
        public array $filters = []
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            search: $request->input('search'),
            per_page: (int) $request->input('per_page', 15),
            sort_by: $request->input('sort_by', 'created_at'),
            sort_direction: $request->input('sort_direction', 'desc'),
            filters: $request->except(['search', 'per_page', 'sort_by', 'sort_direction', 'page'])
        );
    }
}
