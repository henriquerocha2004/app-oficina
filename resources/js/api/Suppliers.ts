import { fetchWithErrorHandling, jsonHeaders } from './BaseApi';
import type { SupplierInterface, SupplierFilterSearchResponse } from '@/pages/suppliers/types';

interface SearchParams {
    search?: string;
    per_page?: number;
    page?: number;
    sort_by?: string;
    sort_direction?: 'asc' | 'desc';
    filters?: Record<string, any>;
}

export const SuppliersApi = {
    search(params: SearchParams) {
        const queryString = new URLSearchParams(
            Object.entries(params)
                .filter(([_, value]) => value !== undefined && value !== '')
                .map(([key, value]) => [
                    key,
                    typeof value === 'object' ? JSON.stringify(value) : String(value)
                ])
        ).toString();

        return fetchWithErrorHandling<SupplierFilterSearchResponse>(
            `/suppliers/filters?${queryString}`,
            { method: 'GET' }
        );
    },

    save(supplier: SupplierInterface) {
        return fetchWithErrorHandling(
            '/suppliers',
            {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify(supplier)
            }
        );
    },

    update(id: string, supplier: SupplierInterface) {
        return fetchWithErrorHandling(
            `/suppliers/${id}`,
            {
                method: 'PUT',
                headers: jsonHeaders(),
                body: JSON.stringify(supplier)
            }
        );
    },

    delete(id: string) {
        return fetchWithErrorHandling(
            `/suppliers/${id}`,
            { 
                method: 'DELETE',
                headers: jsonHeaders()
            }
        );
    },

    getById(id: string) {
        return fetchWithErrorHandling<{ supplier: SupplierInterface }>(
            `/suppliers/${id}`,
            { method: 'GET' }
        );
    }
};
