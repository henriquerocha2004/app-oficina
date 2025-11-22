import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import { defineComponent, h } from 'vue'

vi.mock('@/api/Clients', () => {
    return {
        ClientsApi: {
            search: vi.fn(),
        },
    }
})

import { ClientsApi } from '@/api/Clients'
import { useClientsTable } from '@/pages/clients/composables/useClientsTable'

const FakeHost = defineComponent({
    name: 'FakeHost',
    setup() {
        const api = useClientsTable()
        // expose for tests
        Object.assign((globalThis as any), { __clientsApi: api })
        return () => h('div')
    },
})

describe('useClientsTable composable', () => {
    beforeEach(() => {
        vi.useFakeTimers()
            ; (ClientsApi.search as any).mockReset()
            ; (ClientsApi.search as any).mockResolvedValue({
                clients: {
                    total_items: 2, items: [
                        { id: '01A', name: 'A', email: 'a@a.com', document_number: '123', phone: '11', street: '', city: '', state: '', zip_code: '', observations: '' },
                        { id: '01B', name: 'B', email: 'b@b.com', document_number: '456', phone: '22', street: '', city: '', state: '', zip_code: '', observations: '' },
                    ]
                }
            })
    })

    afterEach(() => {
        vi.useRealTimers()
    })

    it('fetches clients on mount and updates state', async () => {
        mount(FakeHost)
        await flushPromises()

        const { clientsData, totalItems } = (globalThis as any).__clientsApi
        expect(ClientsApi.search).toHaveBeenCalledTimes(1)
        expect(totalItems.value).toBe(2)
        expect(clientsData.value.length).toBe(2)
    })

    it('debounces search and calls API once after delay', async () => {
        mount(FakeHost)
        await flushPromises()

        const { searchTerm } = (globalThis as any).__clientsApi
        searchTerm.value = 'abc'
        searchTerm.value = 'abcd'
        // advance debounce time (400ms)
        await vi.advanceTimersByTimeAsync(400)
        await flushPromises()
        expect(ClientsApi.search).toHaveBeenCalledTimes(2) // initial + debounced
    })

    it('onSortingChange triggers fetch', async () => {
        mount(FakeHost)
        await flushPromises()

        const { onSortingChange } = (globalThis as any).__clientsApi
        onSortingChange([{ id: 'name', desc: true }])
        await flushPromises()
        expect(ClientsApi.search).toHaveBeenCalledTimes(2)
    })

    it('pagination helpers respect bounds', async () => {
        mount(FakeHost)
        await flushPromises()
        const { goToNextPage, goToPreviousPage, currentPage, totalItems } = (globalThis as any).__clientsApi
        // allow pagination
        totalItems.value = 100
        const initial = currentPage.value
        goToNextPage()
        await flushPromises()
        expect(currentPage.value).toBe(initial + 1)
        goToPreviousPage()
        await flushPromises()
        expect(currentPage.value).toBe(initial)
    })
})
