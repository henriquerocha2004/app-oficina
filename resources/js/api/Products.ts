import { fetchWithErrorHandling, jsonHeaders } from './BaseApi';
import type { ProductInterface, ProductFilterSearchResponse, AdjustStockData } from '@/pages/products/types';

interface SearchParams {
    search?: string;
    per_page?: number;
    page?: number;
    sort_by?: string;
    sort_direction?: 'asc' | 'desc';
    filters?: Record<string, any>;
}

export const ProductsApi = {
    search(params: SearchParams) {
        const queryString = new URLSearchParams(
            Object.entries(params)
                .filter(([_, value]) => value !== undefined && value !== '')
                .map(([key, value]) => [
                    key,
                    typeof value === 'object' ? JSON.stringify(value) : String(value)
                ])
        ).toString();

        return fetchWithErrorHandling<ProductFilterSearchResponse>(
            `/products/filters?${queryString}`,
            { method: 'GET' }
        );
    },

    save(product: ProductInterface) {
        return fetchWithErrorHandling(
            '/products',
            {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify(product)
            }
        );
    },

    update(id: string, product: ProductInterface) {
        return fetchWithErrorHandling(
            `/products/${id}`,
            {
                method: 'PUT',
                headers: jsonHeaders(),
                body: JSON.stringify(product)
            }
        );
    },

    delete(id: string) {
        return fetchWithErrorHandling(
            `/products/${id}`,
            { 
                method: 'DELETE',
                headers: jsonHeaders()
            }
        );
    },

    getById(id: string) {
        return fetchWithErrorHandling<{ product: ProductInterface }>(
            `/products/${id}`,
            { method: 'GET' }
        );
    },

    adjustStock(id: string, data: AdjustStockData) {
        return fetchWithErrorHandling(
            `/products/${id}/adjust-stock`,
            {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify(data)
            }
        );
    },

    getLowStock() {
        return fetchWithErrorHandling<{ products: ProductInterface[]; count: number }>(
            '/products/low-stock',
            { method: 'GET' }
        );
    },

    getActiveProducts() {
        return fetchWithErrorHandling<{ products: Array<{ id: string; name: string; sku: string }> }>(
            '/products/active',
            { method: 'GET' }
        );
    },

    // Product Suppliers
    getSuppliers(productId: string) {
        return fetchWithErrorHandling<{ suppliers: any[] }>(
            `/products/${productId}/suppliers`,
            { method: 'GET' }
        );
    },

    attachSupplier(productId: string, data: {
        supplier_id: string;
        supplier_sku?: string;
        cost_price: number;
        lead_time_days?: number;
        min_order_quantity: number;
        is_preferred: boolean;
        notes?: string;
    }) {
        return fetchWithErrorHandling(
            `/products/${productId}/suppliers`,
            {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify(data)
            }
        );
    },

    updateSupplier(productId: string, supplierId: string, data: {
        supplier_sku?: string;
        cost_price: number;
        lead_time_days?: number;
        min_order_quantity: number;
        is_preferred: boolean;
        notes?: string;
    }) {
        return fetchWithErrorHandling(
            `/products/${productId}/suppliers/${supplierId}`,
            {
                method: 'PUT',
                headers: jsonHeaders(),
                body: JSON.stringify(data)
            }
        );
    },

    detachSupplier(productId: string, supplierId: string) {
        return fetchWithErrorHandling(
            `/products/${productId}/suppliers/${supplierId}`,
            {
                method: 'DELETE',
                headers: jsonHeaders()
            }
        );
    }
};
