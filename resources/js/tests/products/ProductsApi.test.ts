import { describe, it, expect, vi, beforeEach } from 'vitest';
import { ProductsApi } from '@/api/Products';

// Mock fetch globally
global.fetch = vi.fn();

describe('ProductsApi', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('search calls correct endpoint with params', async () => {
        const mockResponse = {
            products: {
                items: [],
                total_items: 0,
            },
        };

        vi.mocked(fetch).mockResolvedValueOnce({
            ok: true,
            text: async () => JSON.stringify(mockResponse),
            json: async () => mockResponse,
        } as Response);

        const params = {
            search: 'test',
            page: 1,
            per_page: 15,
        };

        await ProductsApi.search(params);

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/products/filters'),
            expect.any(Object)
        );
    });

    it('adjustStock sends correct data', async () => {
        const mockResponse = {
            message: 'Success',
            product: { id: '1', stock_quantity: 15 },
        };

        vi.mocked(fetch).mockResolvedValueOnce({
            ok: true,
            text: async () => JSON.stringify(mockResponse),
            json: async () => mockResponse,
        } as Response);

        const adjustData = {
            movement_type: 'in' as const,
            quantity: 5,
            reason: 'purchase' as const,
            notes: 'Test purchase',
        };

        await ProductsApi.adjustStock('1', adjustData);

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/products/1/adjust-stock'),
            expect.objectContaining({
                method: 'POST',
                body: JSON.stringify(adjustData),
            })
        );
    });

    it('getLowStock calls correct endpoint', async () => {
        const mockResponse = {
            products: [],
        };

        vi.mocked(fetch).mockResolvedValueOnce({
            ok: true,
            text: async () => JSON.stringify(mockResponse),
            json: async () => mockResponse,
        } as Response);

        await ProductsApi.getLowStock();

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/products/low-stock'),
            expect.objectContaining({ method: 'GET' })
        );
    });

    it('save creates new product', async () => {
        const mockResponse = {
            message: 'Created',
            product: { id: '1', name: 'Test Product' },
        };

        vi.mocked(fetch).mockResolvedValueOnce({
            ok: true,
            text: async () => JSON.stringify(mockResponse),
            json: async () => mockResponse,
        } as Response);

        const productData = {
            name: 'Test Product',
            sku: 'SKU-001',
            category: 'engine',
            unit: 'unit',
            stock_quantity: 10,
            min_stock_level: 5,
            unit_price: 99.99,
            is_active: true,
        };

        await ProductsApi.save(productData);

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/products'),
            expect.objectContaining({
                method: 'POST',
                body: JSON.stringify(productData),
            })
        );
    });

    it('update modifies existing product', async () => {
        const mockResponse = {
            message: 'Updated',
            product: { id: '1', name: 'Updated Product' },
        };

        vi.mocked(fetch).mockResolvedValueOnce({
            ok: true,
            text: async () => JSON.stringify(mockResponse),
            json: async () => mockResponse,
        } as Response);

        const productData = {
            name: 'Updated Product',
            sku: 'SKU-001',
            category: 'engine',
            unit: 'unit',
            stock_quantity: 20,
            min_stock_level: 5,
            unit_price: 99.99,
            is_active: true,
        };

        await ProductsApi.update('1', productData);

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/products/1'),
            expect.objectContaining({
                method: 'PUT',
                body: JSON.stringify(productData),
            })
        );
    });

    it('delete removes product', async () => {
        const mockResponse = {
            message: 'Deleted',
        };

        vi.mocked(fetch).mockResolvedValueOnce({
            ok: true,
            text: async () => JSON.stringify(mockResponse),
            json: async () => mockResponse,
        } as Response);

        await ProductsApi.delete('1');

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/products/1'),
            expect.objectContaining({ method: 'DELETE' })
        );
    });

    // Product-Supplier Association Tests
    describe('Supplier Association', () => {
        it('getSuppliers fetches all suppliers for a product', async () => {
            const mockResponse = {
                suppliers: [
                    {
                        id: 'supplier-1',
                        name: 'Supplier One',
                        cost_price: 100.00,
                        supplier_sku: 'SKU-001',
                        is_preferred: true,
                    },
                ],
            };

            vi.mocked(fetch).mockResolvedValueOnce({
                ok: true,
                text: async () => JSON.stringify(mockResponse),
                json: async () => mockResponse,
            } as Response);

            const result = await ProductsApi.getSuppliers('product-1');

            expect(fetch).toHaveBeenCalledWith(
                expect.stringContaining('/products/product-1/suppliers'),
                expect.objectContaining({ method: 'GET' })
            );
            expect(result).toEqual(mockResponse);
        });

        it('attachSupplier creates product-supplier relationship', async () => {
            const mockResponse = {
                message: 'Fornecedor vinculado com sucesso',
            };

            const supplierData = {
                supplier_id: 'supplier-1',
                cost_price: 150.00,
                supplier_sku: 'SKU-123',
                lead_time_days: 10,
                min_order_quantity: 20,
                is_preferred: true,
                notes: 'Primary supplier',
            };

            vi.mocked(fetch).mockResolvedValueOnce({
                ok: true,
                text: async () => JSON.stringify(mockResponse),
                json: async () => mockResponse,
            } as Response);

            await ProductsApi.attachSupplier('product-1', supplierData);

            expect(fetch).toHaveBeenCalledWith(
                expect.stringContaining('/products/product-1/suppliers'),
                expect.objectContaining({
                    method: 'POST',
                    body: JSON.stringify(supplierData),
                })
            );
        });

        it('updateSupplier updates product-supplier relationship', async () => {
            const mockResponse = {
                message: 'Fornecedor atualizado com sucesso',
            };

            const updateData = {
                cost_price: 120.00,
                supplier_sku: 'UPDATED-SKU',
                lead_time_days: 5,
                min_order_quantity: 15,
                is_preferred: false,
                notes: 'Updated notes',
            };

            vi.mocked(fetch).mockResolvedValueOnce({
                ok: true,
                text: async () => JSON.stringify(mockResponse),
                json: async () => mockResponse,
            } as Response);

            await ProductsApi.updateSupplier('product-1', 'supplier-1', updateData);

            expect(fetch).toHaveBeenCalledWith(
                expect.stringContaining('/products/product-1/suppliers/supplier-1'),
                expect.objectContaining({
                    method: 'PUT',
                    body: JSON.stringify(updateData),
                })
            );
        });

        it('detachSupplier removes product-supplier relationship', async () => {
            const mockResponse = {
                message: 'Fornecedor desvinculado com sucesso',
            };

            vi.mocked(fetch).mockResolvedValueOnce({
                ok: true,
                text: async () => JSON.stringify(mockResponse),
                json: async () => mockResponse,
            } as Response);

            await ProductsApi.detachSupplier('product-1', 'supplier-1');

            expect(fetch).toHaveBeenCalledWith(
                expect.stringContaining('/products/product-1/suppliers/supplier-1'),
                expect.objectContaining({ method: 'DELETE' })
            );
        });
    });
});

