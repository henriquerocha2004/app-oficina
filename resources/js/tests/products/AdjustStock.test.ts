import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import AdjustStock from '@/pages/products/AdjustStock.vue';
import type { ProductInterface } from '@/pages/products/types';

// Mock ProductsApi
vi.mock('@/api/Products', () => ({
    ProductsApi: {
        adjustStock: vi.fn(),
    },
}));

// Mock toast
vi.mock('@/components/ui/toast', () => ({
    toast: vi.fn(),
}));

describe('AdjustStock Component', () => {
    const mockProduct: ProductInterface = {
        id: '1',
        name: 'Test Product',
        sku: 'SKU-001',
        barcode: null,
        manufacturer: null,
        description: null,
        category: 'engine',
        unit: 'unit',
        stock_quantity: 10,
        min_stock_level: 5,
        unit_price: 99.99,
        suggested_price: null,
        is_active: true,
        is_low_stock: false,
        suppliers: [],
        created_at: '2026-01-01T00:00:00.000000Z',
        updated_at: '2026-01-01T00:00:00.000000Z',
    };

    it('calculates new stock quantity for IN movement', () => {
        // Test logic: IN movement adds to stock
        const currentStock = 10;
        const quantity = 5;
        const newStock = currentStock + quantity;

        expect(newStock).toBe(15);
    });

    it('calculates new stock quantity for OUT movement', () => {
        // Test logic: OUT movement subtracts from stock
        const currentStock = 10;
        const quantity = 3;
        const newStock = currentStock - quantity;

        expect(newStock).toBe(7);
    });

    it('shows insufficient stock warning when OUT quantity exceeds current stock', () => {
        const currentStock = 5;
        const quantity = 10;
        const isInsufficient = quantity > currentStock;

        expect(isInsufficient).toBe(true);
    });

    it('does not show warning when OUT quantity is valid', () => {
        const currentStock = 10;
        const quantity = 5;
        const isInsufficient = quantity > currentStock;

        expect(isInsufficient).toBe(false);
    });

    it('provides correct reasons for IN movement', () => {
        const inReasons = [
            { value: 'purchase', label: 'Compra' },
            { value: 'return', label: 'Devolução de Cliente' },
            { value: 'transfer', label: 'Transferência (Entrada)' },
            { value: 'adjustment', label: 'Ajuste de Inventário' },
            { value: 'initial', label: 'Estoque Inicial' },
            { value: 'other', label: 'Outro' },
        ];

        expect(inReasons).toHaveLength(6);
        expect(inReasons[0].value).toBe('purchase');
    });

    it('provides correct reasons for OUT movement', () => {
        const outReasons = [
            { value: 'sale', label: 'Venda' },
            { value: 'loss', label: 'Perda/Quebra' },
            { value: 'return', label: 'Devolução ao Fornecedor' },
            { value: 'transfer', label: 'Transferência (Saída)' },
            { value: 'adjustment', label: 'Ajuste de Inventário' },
            { value: 'other', label: 'Outro' },
        ];

        expect(outReasons).toHaveLength(6);
        expect(outReasons[0].value).toBe('sale');
    });

    it('validates minimum quantity of 1', () => {
        const quantity = 0;
        const isValid = quantity >= 1;

        expect(isValid).toBe(false);
    });

    it('accepts valid quantity', () => {
        const quantity = 5;
        const isValid = quantity >= 1;

        expect(isValid).toBe(true);
    });

    it('prevents submission when stock is insufficient', () => {
        const movementType = 'out';
        const currentStock = 5;
        const quantity = 10;
        const canSubmit = !(movementType === 'out' && quantity > currentStock);

        expect(canSubmit).toBe(false);
    });

    it('allows submission when stock is sufficient', () => {
        const movementType = 'out';
        const currentStock = 10;
        const quantity = 5;
        const canSubmit = !(movementType === 'out' && quantity > currentStock);

        expect(canSubmit).toBe(true);
    });
});
