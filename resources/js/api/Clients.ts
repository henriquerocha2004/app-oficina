import { DefaultResponse } from '@/pages/Shared/Search/DefaultResponse';
import { fetchWithErrorHandling } from './BaseApi';
import type { SearchParams } from '@/pages/Shared/Search/SearchParams';
import type { ClientFilterSearchResponse, ClientInterface } from '@/pages/clients/types';
import clients from "@/routes/clients";

const jsonHeaders = () => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
});

export const ClientsApi = {
    search(params: SearchParams) {
        const client = clients.filters({
            query: {
                page: params.page,
                limit: params.limit,
                search: params.search || undefined,
                sort: params.sort,
                sortField: params.sortField,
                columnSearch: params.columnSearch ? JSON.stringify(params.columnSearch) : undefined,
            }
        })

        return fetchWithErrorHandling<ClientFilterSearchResponse>(client.url, { method: client.method.toUpperCase() });
    },

    save(client: ClientInterface) {
        return fetchWithErrorHandling(
            clients.store.url(),
            {
                method: clients.store().method.toUpperCase(),
                headers: jsonHeaders(),
                body: JSON.stringify(client)
            }) as Promise<DefaultResponse>;
    },

    update(client: ClientInterface) {
        const clientId = client.id || '';

        return fetchWithErrorHandling(
            clients.update.url(clientId),
            {
                method: clients.update(clientId).method.toUpperCase(),
                headers: jsonHeaders(),
                body: JSON.stringify(client)
            }) as Promise<DefaultResponse>;
    },

    remove(id: string) {
        return fetchWithErrorHandling(
            clients.delete.url(id),
            {
                method: clients.delete(id).method.toUpperCase(),
                headers: jsonHeaders()
            }) as Promise<DefaultResponse>;
    }
};