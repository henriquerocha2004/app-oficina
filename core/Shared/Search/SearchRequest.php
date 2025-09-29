<?php

namespace AppOficina\Shared\Search;

class SearchRequest
{
    public int $limit = 10;
    public string $search = '';
    public string $sortField = 'id';
    public string $sort = 'asc';
    public int $page = 1;
    public array $columnSearch = [];
    private int $offset = 0;

    public function __construct(
        int $limit = 10,
        string $search = '',
        string $sortField = 'id',
        string $sort = 'asc',
        int $page = 1,
        array $columnSearch = []
    ) {
        $this->limit = $limit;
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sort = $sort;
        $this->page = $page;
        $this->columnSearch = $columnSearch;
    }

    public function getOffset(): int
    {
        if ($this->limit > 0) {
            $this->offset = ($this->page - 1) * $this->limit;
        }
        return $this->offset;
    }
}
