import { describe, it, expect, vi, beforeEach } from 'vitest';
import { defineComponent, nextTick } from 'vue';
import { mount } from '@vue/test-utils';
import { useProductsTable } from '@/pages/products/composables/useProductsTable';
import { ProductsApi } from '@/api/Products';

// Mock ProductsApi
vi.mock('@/api/Products', () => ({
    ProductsApi: {
        search: vi.fn(),
        adjustStock: vi.fn(),
        getLowStock: vi.fn(),
    },
}));

// Helper to test composables with lifecycle hooks
function withSetup<T>(composable: () => T): [T, any] {
    let result: T;
    const app = defineComponent({
        setup() {
            result = composable();
            return () => {};
        },
    });
    const wrapper = mount(app);
    return [result!, wrapper];
}

describe('useProductsTable', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('initializes with default values', () => {
        const {
            productsData,
            totalItems,
            currentPage,
            pageSize,
            searchTerm,
            filterLowStock,
            isLoading,
        } = useProductsTable();

        expect(productsData.value).toEqual([]);
        expect(totalItems.value).toBe(0);
        expect(currentPage.value).toBe(1);
        expect(pageSize.value).toBe(10);
        expect(searchTerm.value).toBe('');
        expect(filterLowStock.value).toBe(false);
        expect(isLoading.value).toBe(false);
    });

    it('fetches products on mount', async () => {
        const mockProducts = [
            {
                id: '1',
                name: 'Product 1',
                sku: 'SKU-001',
                stock_quantity: 10,
                min_stock_level: 5,
                unit_price: 99.99,
                category: 'engine',
                unit: 'unit',
                is_active: true,
                is_low_stock: false,
            },
        ];

        vi.mocked(ProductsApi.search).mockResolvedValue({
            products: {
                items: mockProducts,
                total_items: 1,
            },
        });

        const [{ productsData, totalItems }] = withSetup(() => useProductsTable());

        // Wait for onMounted and API call
        await vi.waitFor(() => {
            expect(ProductsApi.search).toHaveBeenCalled();
        });
        
        await nextTick();

        expect(productsData.value).toEqual(mockProducts);
        expect(totalItems.value).toBe(1);
    });

    it('debounces search term changes', async () => {
        vi.mocked(ProductsApi.search).mockResolvedValue({
            products: {
                items: [],
                total_items: 0,
            },
        });

        const { searchTerm } = useProductsTable();

        searchTerm.value = 'test';
        searchTerm.value = 'test product';
        searchTerm.value = 'test product name';

        // Wait less than debounce time
        await new Promise(resolve => setTimeout(resolve, 300));
        expect(ProductsApi.search).not.toHaveBeenCalled();

        // Wait for debounce time
        await new Promise(resolve => setTimeout(resolve, 200));
        expect(ProductsApi.search).toHaveBeenCalledOnce();
    });

    it('filters low stock products when enabled', async () => {
        vi.mocked(ProductsApi.search).mockResolvedValue({
            products: {
                items: [],
                total_items: 0,
            },
        });

        const [{ filterLowStock }] = withSetup(() => useProductsTable());
        
        // Aguarda a primeira chamada do onMounted
        await vi.waitFor(() => {
            expect(ProductsApi.search).toHaveBeenCalled();
        });
        
        vi.clearAllMocks();

        // Atualiza o valor do filtro diretamente (o watch vai disparar a busca)
        filterLowStock.value = true;

        await vi.waitFor(() => {
            expect(ProductsApi.search).toHaveBeenCalled();
        });

        expect(filterLowStock.value).toBe(true);
        expect(ProductsApi.search).toHaveBeenCalledWith(
            expect.objectContaining({
                filters: { low_stock: true }
            })
        );
    });

    it('navigates to next page', async () => {
        vi.mocked(ProductsApi.search).mockResolvedValue({
            products: {
                items: [],
                total_items: 50,
            },
        });

        const [{ currentPage, goToNextPage }] = withSetup(() => useProductsTable());

        // Aguarda primeira chamada
        await vi.waitFor(() => {
            expect(ProductsApi.search).toHaveBeenCalled();
        });

        goToNextPage();
        await nextTick();

        expect(currentPage.value).toBe(2);
    });

    it('navigates to previous page', async () => {
        vi.mocked(ProductsApi.search).mockResolvedValue({
            products: {
                items: [],
                total_items: 50,
            },
        });

        const { currentPage, goToPreviousPage } = useProductsTable();

        currentPage.value = 3;
        await new Promise(resolve => setTimeout(resolve, 100));

        goToPreviousPage();

        expect(currentPage.value).toBe(2);
    });

    it('does not go below page 1', async () => {
        const { currentPage, goToPreviousPage } = useProductsTable();

        expect(currentPage.value).toBe(1);
        goToPreviousPage();
        expect(currentPage.value).toBe(1);
    });
});
