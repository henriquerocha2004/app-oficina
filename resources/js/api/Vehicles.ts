import { DefaultResponse } from '@/pages/Shared/Search/DefaultResponse';
import { fetchWithErrorHandling } from './BaseApi';
import type { SearchParams } from '@/pages/Shared/Search/SearchParams';
import type { VehicleFilterSearchResponse, VehiclesInterface } from '@/pages/vehicles/types';
import vehicles from "@/routes/vehicles";

const jsonHeaders = () => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
});

export const VehiclesApi = {
    search(params: SearchParams) {
        const filter = vehicles.filters({
            query: {
                page: params.page,
                limit: params.limit,
                search: params.search || undefined,
                sort: params.sort,
                sortField: params.sortField,
                columnSearch: params.columnSearch ? JSON.stringify(params.columnSearch) : undefined,
            }
        })

        return fetchWithErrorHandling<VehicleFilterSearchResponse>(filter.url, { method: filter.method.toUpperCase() });
    },

    save(client: VehiclesInterface) {
        return fetchWithErrorHandling(
            vehicles.store.url(),
            {
                method: vehicles.store().method.toUpperCase(),
                headers: jsonHeaders(),
                body: JSON.stringify(client)
            }) as Promise<DefaultResponse>;
    },

    update(vehicle: VehiclesInterface) {
        const vehicleId = vehicle.id || '';

        return fetchWithErrorHandling(
            vehicles.update.url(vehicleId),
            {
                method: vehicles.update(vehicleId).method.toUpperCase(),
                headers: jsonHeaders(),
                body: JSON.stringify(vehicle)
            }) as Promise<DefaultResponse>;
    },

    remove(id: string) {
        return fetchWithErrorHandling(
            vehicles.delete.url(id),
            {
                method: vehicles.delete(id).method.toUpperCase(),
                headers: jsonHeaders()
            }) as Promise<DefaultResponse>;
    }
};