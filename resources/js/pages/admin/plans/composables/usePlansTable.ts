import { ref, watch, onMounted } from 'vue';
import type { SortingState } from '@tanstack/vue-table';
import type { SubscriptionPlan } from '../types';
import axios from 'axios';
import admin from '@/routes/admin';

interface PlansResponse {
    plans: {
        items: SubscriptionPlan[];
        total_items: number;
    };
}

export function usePlansTable() {
    const plansData = ref<SubscriptionPlan[]>([]);
    const totalItems = ref(0);
    const loading = ref(false);

    const currentPage = ref(1);
    const pageSize = ref(10);
    const searchTerm = ref('');
    const sorting = ref<SortingState>([]);

    const fetchPlans = async () => {
        loading.value = true;
        try {
            const params = {
                page: currentPage.value,
                per_page: pageSize.value,
                search: searchTerm.value,
                sort_by: sorting.value[0]?.id || 'id',
                sort_direction: sorting.value[0]?.desc ? 'desc' : 'asc',
            };

            const response = await axios.get<PlansResponse>(admin.plans.filters.url(), { params });
            
           plansData.value = response.data.plans.items || [];
           totalItems.value = response.data.plans.total_items || 0;
        } catch (error) {
            console.error('Erro ao buscar planos:', error);
            plansData.value = [];
            totalItems.value = 0;
        } finally {
            loading.value = false;
        }
    };

    let searchDebounce: ReturnType<typeof setTimeout> | null = null;
    watch(searchTerm, () => {
        currentPage.value = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchPlans, 400);
    });

    const onSortingChange = (next: SortingState) => {
        sorting.value = next;
        fetchPlans();
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
            fetchPlans();
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchPlans();
        }
    };

    onMounted(fetchPlans);

    return {
        plansData,
        totalItems,
        loading,
        currentPage,
        pageSize,
        searchTerm,
        sorting,
        fetchPlans,
        onSortingChange,
        goToNextPage,
        goToPreviousPage,
    };
}
