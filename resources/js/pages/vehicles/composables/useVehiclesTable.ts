import { ref, watch, onMounted } from 'vue';
import type { SortingState } from '@tanstack/vue-table';
import type { VehicleFilterSearchResponse, VehiclesInterface } from '../types';
import type { SearchParams } from '@/pages/Shared/Search/SearchParams';
import { VehiclesApi } from '@/api/Vehicles';

export function useVehiclesTable() {
    const vehiclesData = ref<VehiclesInterface[]>([]);
    const totalItems = ref(0);
    const loading = ref(false);

    const currentPage = ref(1);
    const pageSize = ref(10);
    const searchTerm = ref('');
    const vehicleType = ref<string>('');
    const sorting = ref<SortingState>([]);

    const fetchCars = async () => {
        loading.value = true;
        try {
            const params: SearchParams = {
                per_page: pageSize.value,
                page: currentPage.value,
                search: searchTerm.value,
                sort_direction: sorting.value[0]?.desc ? 'desc' : 'asc',
                sort_by: sorting.value[0]?.id || 'id',
            };

            // Adicionar filtro de tipo de veículo se selecionado
            if (vehicleType.value) {
                params.vehicle_type = vehicleType.value;
            }

            const response: VehicleFilterSearchResponse = await VehiclesApi.search(params);
            vehiclesData.value = response.vehicles.items;
            totalItems.value = response.vehicles.total_items;
        } finally {
            loading.value = false;
        }
    };

    let searchDebounce: ReturnType<typeof setTimeout> | null = null;
    watch(searchTerm, () => {
        currentPage.value = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchCars, 400);
    });

    // Watch para filtro de tipo de veículo
    watch(vehicleType, () => {
        currentPage.value = 1;
        fetchCars();
    });

    const onSortingChange = (next: SortingState) => {
        sorting.value = next;
        fetchCars();
    };

    const goToNextPage = () => {
        if (currentPage.value * pageSize.value < totalItems.value) {
            currentPage.value++;
            fetchCars();
        }
    };

    const goToPreviousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            fetchCars();
        }
    };

    onMounted(fetchCars);

    return {
        vehiclesData, totalItems, loading,
        currentPage, pageSize, searchTerm, vehicleType, sorting,
        fetchCars, onSortingChange, goToNextPage, goToPreviousPage,
    };
}