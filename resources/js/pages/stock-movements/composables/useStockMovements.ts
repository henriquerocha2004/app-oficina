import { ref, watch, onMounted } from 'vue';
import { StockMovementsApi } from '@/api/StockMovements';
import type { StockMovementDetailInterface, StockMovementFilters } from '../types';
import type { SortingState } from '@tanstack/vue-table';

export function useStockMovements() {
    const movementsData = ref<StockMovementDetailInterface[]>([]);
    const totalItems = ref(0);
    const currentPage = ref(1);
    const pageSize = ref(15);
    const isLoading = ref(false);
    const sorting = ref<SortingState>([{ id: 'created_at', desc: true }]);
    
    // Filters
    const filters = ref<StockMovementFilters>({
        product_id: undefined,
        movement_type: undefined,
        reason: undefined,
        user_id: undefined,
        date_from: undefined,
        date_to: undefined,
    });

    const fetchMovements = async () => {
        isLoading.value = true;
        try {
            const params = {
                page: currentPage.value,
                per_page: pageSize.value,
                sort_by: sorting.value[0]?.id || 'created_at',
                sort_direction: sorting.value[0]?.desc ? 'desc' : 'asc',
                ...filters.value,
            };

            const response = await StockMovementsApi.search(params);
            movementsData.value = response.data;
            totalItems.value = response.total;
        } catch (error) {
            console.error('Error fetching stock movements:', error);
            movementsData.value = [];
            totalItems.value = 0;
        } finally {
            isLoading.value = false;
        }
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    };

    const updateFilters = (newFilters: Partial<StockMovementFilters>) => {
        filters.value = { ...filters.value, ...newFilters };
        currentPage.value = 1; // Reset to first page when filters change
    };

    const clearFilters = () => {
        filters.value = {
            product_id: undefined,
            movement_type: undefined,
            reason: undefined,
            user_id: undefined,
            date_from: undefined,
            date_to: undefined,
        };
        currentPage.value = 1;
    };

    watch([currentPage, sorting], () => {
        fetchMovements();
    });

    watch(filters, () => {
        fetchMovements();
    }, { deep: true });

    onMounted(() => {
        fetchMovements();
    });

    return {
        movementsData,
        totalItems,
        currentPage,
        pageSize,
        isLoading,
        sorting,
        filters,
        goToNextPage,
        goToPreviousPage,
        updateFilters,
        clearFilters,
    };
}
