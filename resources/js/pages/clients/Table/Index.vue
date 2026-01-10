<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { valueUpdater } from '@/lib/utils';
import type { ColumnDef, ColumnFiltersState } from '@tanstack/vue-table';
import { FlexRender, getCoreRowModel, getFilteredRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from '@tanstack/vue-table';
import { ClientInterface } from '../types';
import { useClientsTable } from '@/pages/clients/composables/useClientsTable';
import { ref } from 'vue';

const props = defineProps<{
    columns: ColumnDef<ClientInterface>[];
}>();

const {
    clientsData, totalItems,
    currentPage, pageSize, searchTerm, sorting,
    fetchClients, onSortingChange, goToNextPage, goToPreviousPage
} = useClientsTable();

const columnFilters = ref<ColumnFiltersState>([]);
const emit = defineEmits(['create']);

const table = useVueTable({
    get data() {
        return clientsData.value;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onSortingChange: (updaterOrValue: any) => {
        const next = typeof updaterOrValue === 'function' ? updaterOrValue(sorting.value) : updaterOrValue;
        onSortingChange(next);
    },
    onColumnFiltersChange: (updaterOrValue: any) => {
        valueUpdater(updaterOrValue, columnFilters)
        fetchClients();
    },
    getFilteredRowModel: getFilteredRowModel(),
    manualPagination: true,
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
    },
});

defineExpose({
    fetchClients,
});

</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex justify-between py-4">
            <Input class="max-w-sm" placeholder="Pesquisar ..." v-model="searchTerm" />
            <div>
                <Button @click="emit('create')" variant="default">Novo Cliente</Button>
            </div>
        </div>
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() ? 'selected' : undefined">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell :colspan="columns.length" class="h-24 text-center"> No results. </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
        <div class="flex items-center justify-end space-x-2 py-4">
            <Button variant="outline" size="sm" :disabled="currentPage <= 1" @click="goToPreviousPage">
                Página Anterior
            </Button>
            <Button variant="outline" size="sm" :disabled="currentPage * pageSize >= totalItems" @click="goToNextPage">
                Próxima Página
            </Button>
        </div>
    </div>
</template>
