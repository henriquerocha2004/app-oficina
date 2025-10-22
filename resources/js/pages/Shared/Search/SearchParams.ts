type SortOrder = 'asc' | 'desc';

export interface ColumnSearch {
    field: string;
    value: string;
}

export interface SearchParams {
    page: number;
    limit: number;
    sort: SortOrder;
    sortField: string;
    search?: string | null;
    columnSearch?: ColumnSearch[] | null;
}