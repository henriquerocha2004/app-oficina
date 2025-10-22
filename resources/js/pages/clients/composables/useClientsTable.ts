import { ref, watch, onMounted } from 'vue';
import type { SortingState } from '@tanstack/vue-table';
import type { ClientFilterSearchResponse, ClientInterface } from '../types';
import type { SearchParams } from '@/pages/Shared/Search/SearchParams';
import { ClientsApi } from '@/api/Clients';

export function useClientsTable() {
    const clientsData = ref<ClientInterface[]>([]);
    const totalItems = ref(0);
    const loading = ref(false);

    const currentPage = ref(1);
    const pageSize = ref(10);
    const searchTerm = ref('');
    const sorting = ref<SortingState>([]);

    const fetchClients = async () => {
        loading.value = true;
        try {
            const params: SearchParams = {
                limit: pageSize.value,
                page: currentPage.value,
                search: searchTerm.value,
                sort: sorting.value[0]?.desc ? 'desc' : 'asc',
                sortField: sorting.value[0]?.id || 'id',
            };
            const response: ClientFilterSearchResponse = await ClientsApi.search(params);
            clientsData.value = response.clients.items;
            totalItems.value = response.clients.totalItems;
        } finally {
            loading.value = false;
        }
    };

    // debounce de busca
    let searchDebounce: ReturnType<typeof setTimeout> | null = null;
    watch(searchTerm, () => {
        currentPage.value = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchClients, 400);
    });

    const onSortingChange = (next: SortingState) => {
        sorting.value = next;
        fetchClients();
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
            fetchClients();
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchClients();
        }
    };

    onMounted(fetchClients);

    return {
        // data
        clientsData, totalItems, loading,
        // estado
        currentPage, pageSize, searchTerm, sorting,
        // ações
        fetchClients, onSortingChange, goToNextPage, goToPreviousPage,
    };
}