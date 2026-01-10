import { fetchWithErrorHandling } from './BaseApi';
import type { StockMovementInterface } from '@/pages/products/types';
import type { StockMovementDetailInterface } from '@/pages/stock-movements/types';

interface SearchParams {
    search?: string;
    per_page?: number;
    page?: number;
    sort_by?: string;
    sort_direction?: 'asc' | 'desc';
    product_id?: string;
    movement_type?: 'in' | 'out';
    reason?: string;
    user_id?: string;
    date_from?: string;
    date_to?: string;
}

interface MovementsResult {
    items: StockMovementDetailInterface[];
    total_items: number;
}

export const StockMovementsApi = {
    async search(params: SearchParams) {
        const queryString = new URLSearchParams(
            Object.entries(params)
                .filter(([_, value]) => value !== undefined && value !== '')
                .map(([key, value]) => [key, String(value)])
        ).toString();

        const response = await fetchWithErrorHandling<{ movements: MovementsResult }>(
            `/stock-movements/filters?${queryString}`,
            { method: 'GET' }
        );

        return {
            data: response.movements.items,
            total: response.movements.total_items,
        };
    },

    getByProduct(productId: string) {
        return fetchWithErrorHandling<{ movements: StockMovementInterface[] }>(
            `/stock-movements/product/${productId}`,
            { method: 'GET' }
        );
    }
};
