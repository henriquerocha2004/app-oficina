import { mount } from '@vue/test-utils'
import TableIndex from '@/pages/clients/Table/Index.vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { ref } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'
import type { ClientInterface } from '@/pages/clients/types'

// Mock the composable to control state/behaviors
const mockedApi = {
    clientsData: ref<any[]>([]),
    totalItems: ref(0),
    currentPage: ref(1),
    pageSize: ref(10),
    searchTerm: ref(''),
    sorting: ref([] as any[]),
    fetchClients: vi.fn(),
    onSortingChange: vi.fn(),
    goToNextPage: vi.fn(),
    goToPreviousPage: vi.fn(),
}

vi.mock('@/pages/clients/composables/useClientsTable', () => ({
    useClientsTable: () => mockedApi,
}))

// We'll stub UI components using global stubs during mount to avoid hoisting issues

describe('Clients Table Index.vue', () => {
    const columns: ColumnDef<ClientInterface>[] = [
        { accessorKey: 'name', header: 'Nome' } as any,
        { accessorKey: 'email', header: 'Email' } as any,
    ]

    beforeEach(() => {
        mockedApi.clientsData.value = []
        mockedApi.totalItems.value = 0
        mockedApi.currentPage.value = 1
        mockedApi.pageSize.value = 10
        mockedApi.searchTerm.value = ''
        mockedApi.fetchClients.mockClear()
        mockedApi.goToNextPage.mockClear()
        mockedApi.goToPreviousPage.mockClear()
    })

    it('renders No results when empty and emits create', async () => {
        const wrapper = mount(TableIndex, {
            props: { columns },
            global: {
                stubs: {
                    Button: { template: '<button @click="$emit(\'click\')"><slot /></button>' },
                    Input: { template: '<input :value="modelValue" :placeholder="placeholder" @input="$emit(\'update:modelValue\', $event.target.value)" />', props: ['modelValue', 'placeholder'] },
                    Table: { template: '<table><slot /></table>' },
                    TableBody: { template: '<tbody><slot /></tbody>' },
                    TableCell: { template: '<td><slot /></td>' },
                    TableHead: { template: '<th><slot /></th>' },
                    TableHeader: { template: '<thead><slot /></thead>' },
                    TableRow: { template: '<tr><slot /></tr>' },
                }
            }
        })
        expect(wrapper.text()).toContain('No results')

        // click create
        await wrapper.get('button').trigger('click')
        expect(wrapper.emitted('create')).toBeTruthy()

        // exposes fetchClients
        expect(typeof (wrapper.vm as any).fetchClients).toBe('function')
    })

    it('binds searchTerm via v-model to input', async () => {
        const wrapper = mount(TableIndex, { props: { columns }, global: { stubs: { Button: { template: '<button @click="$emit(\'click\')"><slot /></button>' }, Input: { template: '<input :value="modelValue" :placeholder="placeholder" @input="$emit(\'update:modelValue\', $event.target.value)" />', props: ['modelValue', 'placeholder'] }, Table: { template: '<table><slot /></table>' }, TableBody: { template: '<tbody><slot /></tbody>' }, TableCell: { template: '<td><slot /></td>' }, TableHead: { template: '<th><slot /></th>' }, TableHeader: { template: '<thead><slot /></thead>' }, TableRow: { template: '<tr><slot /></tr>' } } } })
        const input = wrapper.get('input')
        await input.setValue('abc')
        expect(mockedApi.searchTerm.value).toBe('abc')
    })

    it('disables previous button on first page and next when at end', async () => {
        const wrapper = mount(TableIndex, { props: { columns }, global: { stubs: { Button: { template: '<button @click="$emit(\'click\')"><slot /></button>' }, Input: { template: '<input :value="modelValue" :placeholder="placeholder" @input="$emit(\'update:modelValue\', $event.target.value)" />', props: ['modelValue', 'placeholder'] }, Table: { template: '<table><slot /></table>' }, TableBody: { template: '<tbody><slot /></tbody>' }, TableCell: { template: '<td><slot /></td>' }, TableHead: { template: '<th><slot /></th>' }, TableHeader: { template: '<thead><slot /></thead>' }, TableRow: { template: '<tr><slot /></tr>' } } } })
        const prev = wrapper.findAll('button').find(b => b.text().includes('Página Anterior'))!
        const next = wrapper.findAll('button').find(b => b.text().includes('Próxima Página'))!
        expect(prev.attributes('disabled')).toBeDefined()

        mockedApi.totalItems.value = 10
        mockedApi.currentPage.value = 1
        mockedApi.pageSize.value = 10
        await wrapper.vm.$forceUpdate()
        expect(next.attributes('disabled')).toBeDefined()
    })
})
