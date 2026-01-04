import { ref, watch, onMounted } from 'vue';
import type { Ref } from 'vue';
import { SuppliersApi } from '@/api/Suppliers';
import type { SupplierInterface } from '../types';
import type { SortingState } from '@tanstack/vue-table';

export function useSuppliersTable() {
    const suppliersData: Ref<SupplierInterface[]> = ref([]);
    const totalItems = ref(0);
    const currentPage = ref(1);
    const pageSize = ref(10);
    const searchTerm = ref('');
    const sorting: Ref<SortingState> = ref([]);
    const isLoading = ref(false);

    let searchDebounce: ReturnType<typeof setTimeout> | null = null;

    const fetchSuppliers = async () => {
        isLoading.value = true;
        try {
            const response = await SuppliersApi.search({
                per_page: pageSize.value,
                page: currentPage.value,
                search: searchTerm.value,
                sort_by: sorting.value[0]?.id || 'created_at',
                sort_direction: sorting.value[0]?.desc ? 'desc' : 'asc',
            });
            suppliersData.value = response.suppliers.items;
            totalItems.value = response.suppliers.total_items;
        } catch (error) {
            console.error('Error fetching suppliers:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
            fetchSuppliers();
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchSuppliers();
        }
    };

    watch(searchTerm, () => {
        currentPage.value = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            fetchSuppliers();
        }, 400);
    });

    watch(sorting, () => {
        fetchSuppliers();
    });

    onMounted(() => {
        fetchSuppliers();
    });

    return {
        suppliersData,
        totalItems,
        currentPage,
        pageSize,
        searchTerm,
        sorting,
        isLoading,
        fetchSuppliers,
        goToNextPage,
        goToPreviousPage,
    };
}

// Alias export for compatibility
export const useSuppliers = useSuppliersTable;
