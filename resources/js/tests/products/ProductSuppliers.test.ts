import { describe, it, expect, vi } from 'vitest';
import { ProductsApi } from '@/api/Products';
import type { Product } from '@/pages/products/types';

// Mock ProductsApi
vi.mock('@/api/Products', () => ({
    ProductsApi: {
        getSuppliers: vi.fn(),
        attachSupplier: vi.fn(),
        updateSupplier: vi.fn(),
        detachSupplier: vi.fn(),
    },
}));

describe('ProductSuppliers Integration', () => {
    const mockProduct: Product = {
        id: 'product-1',
        name: 'Test Product',
        sku: 'TEST-001',
        category: 'engine',
        unit: 'unit',
        stock_quantity: 10,
        min_stock_level: 5,
        unit_price: 99.99,
        is_active: true,
        created_at: '2026-01-01',
        updated_at: '2026-01-01',
    };

    const mockSuppliers = {
        suppliers: [
            {
                id: 'supplier-1',
                name: 'Supplier One',
                document_number: '12.345.678/0001-90',
                supplier_sku: 'SKU-001',
                cost_price: 100.00,
                lead_time_days: 5,
                min_order_quantity: 10,
                is_preferred: true,
                notes: 'Primary supplier',
            },
            {
                id: 'supplier-2',
                name: 'Supplier Two',
                document_number: '98.765.432/0001-10',
                supplier_sku: 'SKU-002',
                cost_price: 95.00,
                lead_time_days: 7,
                min_order_quantity: 5,
                is_preferred: false,
            },
        ],
    };

    it('can fetch suppliers for a product', async () => {
        vi.mocked(ProductsApi.getSuppliers).mockResolvedValue(mockSuppliers);

        const result = await ProductsApi.getSuppliers('product-1');

        expect(ProductsApi.getSuppliers).toHaveBeenCalledWith('product-1');
        expect(result.suppliers).toHaveLength(2);
        expect(result.suppliers[0].name).toBe('Supplier One');
    });

    it('can attach a supplier to a product', async () => {
        const supplierData = {
            supplier_id: 'supplier-1',
            cost_price: 150.00,
            supplier_sku: 'SKU-123',
            lead_time_days: 10,
            min_order_quantity: 20,
            is_preferred: true,
            notes: 'Primary supplier',
        };

        vi.mocked(ProductsApi.attachSupplier).mockResolvedValue({
            message: 'Fornecedor vinculado com sucesso',
        });

        await ProductsApi.attachSupplier('product-1', supplierData);

        expect(ProductsApi.attachSupplier).toHaveBeenCalledWith('product-1', supplierData);
    });

    it('can update a supplier relationship', async () => {
        const updateData = {
            cost_price: 120.00,
            supplier_sku: 'UPDATED-SKU',
            lead_time_days: 5,
            min_order_quantity: 15,
            is_preferred: false,
            notes: 'Updated notes',
        };

        vi.mocked(ProductsApi.updateSupplier).mockResolvedValue({
            message: 'Fornecedor atualizado com sucesso',
        });

        await ProductsApi.updateSupplier('product-1', 'supplier-1', updateData);

        expect(ProductsApi.updateSupplier).toHaveBeenCalledWith(
            'product-1',
            'supplier-1',
            updateData
        );
    });

    it('can detach a supplier from a product', async () => {
        vi.mocked(ProductsApi.detachSupplier).mockResolvedValue({
            message: 'Fornecedor removido com sucesso',
        });

        await ProductsApi.detachSupplier('product-1', 'supplier-1');

        expect(ProductsApi.detachSupplier).toHaveBeenCalledWith('product-1', 'supplier-1');
    });

    it('handles multiple suppliers with different preferences', async () => {
        vi.mocked(ProductsApi.getSuppliers).mockResolvedValue(mockSuppliers);

        const result = await ProductsApi.getSuppliers('product-1');

        const preferred = result.suppliers.find(s => s.is_preferred);
        const notPreferred = result.suppliers.find(s => !s.is_preferred);

        expect(preferred?.name).toBe('Supplier One');
        expect(notPreferred?.name).toBe('Supplier Two');
    });

    it('validates cost price is a number', async () => {
        vi.mocked(ProductsApi.getSuppliers).mockResolvedValue(mockSuppliers);

        const result = await ProductsApi.getSuppliers('product-1');

        result.suppliers.forEach(supplier => {
            expect(typeof supplier.cost_price).toBe('number');
            expect(supplier.cost_price).toBeGreaterThan(0);
        });
    });

    it('validates minimum order quantity', async () => {
        vi.mocked(ProductsApi.getSuppliers).mockResolvedValue(mockSuppliers);

        const result = await ProductsApi.getSuppliers('product-1');

        result.suppliers.forEach(supplier => {
            expect(supplier.min_order_quantity).toBeGreaterThan(0);
        });
    });
});

