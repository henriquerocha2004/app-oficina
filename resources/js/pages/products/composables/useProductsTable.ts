import { ref, watch, onMounted } from 'vue';
import type { Ref } from 'vue';
import { ProductsApi } from '@/api/Products';
import type { ProductInterface } from '../types';
import type { SortingState } from '@tanstack/vue-table';

export function useProductsTable() {
    const productsData: Ref<ProductInterface[]> = ref([]);
    const totalItems = ref(0);
    const currentPage = ref(1);
    const pageSize = ref(10);
    const searchTerm = ref('');
    const sorting: Ref<SortingState> = ref([]);
    const filterLowStock = ref(false);
    const isLoading = ref(false);

    let searchDebounce: ReturnType<typeof setTimeout> | null = null;

    const fetchProducts = async () => {
        isLoading.value = true;
        try {
            const params: any = {
                per_page: pageSize.value,
                page: currentPage.value,
                search: searchTerm.value,
                sort_by: sorting.value[0]?.id || 'created_at',
                sort_direction: sorting.value[0]?.desc ? 'desc' : 'asc',
            };

            if (filterLowStock.value) {
                params.filters = { low_stock: true };
            }

            const response = await ProductsApi.search(params);
            productsData.value = response.products.items;
            totalItems.value = response.products.total_items;
        } catch (error) {
            console.error('Error fetching products:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
            fetchProducts();
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchProducts();
        }
    };

    watch(searchTerm, () => {
        currentPage.value = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            fetchProducts();
        }, 400);
    });

    watch(sorting, () => {
        fetchProducts();
    });

    watch(filterLowStock, () => {
        currentPage.value = 1;
        fetchProducts();
    });

    onMounted(() => {
        fetchProducts();
    });

    return {
        productsData,
        totalItems,
        currentPage,
        pageSize,
        searchTerm,
        sorting,
        filterLowStock,
        isLoading,
        fetchProducts,
        goToNextPage,
        goToPreviousPage,
        refresh: fetchProducts,
    };
}
