type SortOrder = 'asc' | 'desc';

export interface SearchParams {
    page: number;
    per_page: number;
    sort_direction: SortOrder;
    sort_by: string;
    search?: string | null;
    vehicle_type?: string;
    [key: string]: string | number | null | undefined;
}