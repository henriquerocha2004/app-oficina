import { ref, watch, onMounted } from 'vue';
import type { SortingState } from '@tanstack/vue-table';
import type { Tenant } from '../types';
import axios from 'axios';
import admin from '@/routes/admin';

interface TenantsResponse {
    tenants: {
        items: Tenant[];
        total_items: number;
    };
}

export function useTenantsTable() {
    const tenantsData = ref<Tenant[]>([]);
    const totalItems = ref(0);
    const loading = ref(false);

    const currentPage = ref(1);
    const pageSize = ref(10);
    const searchTerm = ref('');
    const sorting = ref<SortingState>([]);

    const fetchTenants = async () => {
        loading.value = true;
        try {
            const params = {
                page: currentPage.value,
                per_page: pageSize.value,
                search: searchTerm.value,
                sort_by: sorting.value[0]?.id || 'id',
                sort_direction: sorting.value[0]?.desc ? 'desc' : 'asc',
            };

            const response = await axios.get<TenantsResponse>(admin.tenants.filters.url(), {
                params,
            });
            
            tenantsData.value = response.data.tenants.items || [];
            totalItems.value = response.data.tenants.total_items || 0;
        } catch (error) {
            console.error('Erro ao buscar tenants:', error);
            tenantsData.value = [];
            totalItems.value = 0;
        } finally {
            loading.value = false;
        }
    };

    let searchDebounce: ReturnType<typeof setTimeout> | null = null;
    watch(searchTerm, () => {
        currentPage.value = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchTenants, 400);
    });

    const onSortingChange = (next: SortingState) => {
        sorting.value = next;
        fetchTenants();
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
            fetchTenants();
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchTenants();
        }
    };

    onMounted(fetchTenants);

    return {
        tenantsData,
        totalItems,
        loading,
        currentPage,
        pageSize,
        searchTerm,
        sorting,
        fetchTenants,
        onSortingChange,
        goToNextPage,
        goToPreviousPage,
    };
}
