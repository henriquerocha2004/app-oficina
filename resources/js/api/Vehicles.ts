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
                per_page: params.per_page,
                search: params.search || undefined,
                sort_direction: params.sort_direction,
                sort_by: params.sort_by,
                vehicle_type: params.vehicle_type || undefined,
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
    },

    getByClientId(clientId: string) {
        const route = vehicles.client(clientId);
        return fetchWithErrorHandling<{ vehicles: VehiclesInterface[] }>(
            route.url,
            { method: route.method.toUpperCase() }
        );
    }
};