<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { valueUpdater } from '@/lib/utils';
import type { ColumnDef, ColumnFiltersState } from '@tanstack/vue-table';
import { FlexRender, getCoreRowModel, getFilteredRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from '@tanstack/vue-table';
import { VehiclesInterface } from '../types';
import { useVehiclesTable } from '@/pages/vehicles/composables/useVehiclesTable';
import { ref } from 'vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'

const props = defineProps<{
    columns: ColumnDef<VehiclesInterface>[];
}>();

const {
    vehiclesData, totalItems,
    currentPage, pageSize, searchTerm, sorting,
    fetchCars, onSortingChange, goToNextPage, goToPreviousPage
} = useVehiclesTable();

const columnFilters = ref<ColumnFiltersState>([]);
const emit = defineEmits(['create']);

const vehicles: VehiclesInterface[] = [
    {
        id: '1',
        model: 'Toyota Corolla',
        year: 2020,
        licensePlate: 'ABC1234',
        type: 'car',
        client: 'John Doe',
        phone: '555-1234',
        last_service_date: '2023-01-01',
        status: 'active',
    }
]


const table = useVueTable({
    get data() {
        return vehicles;
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
        fetchCars();
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
    fetchCars,
});

</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex justify-between py-4">
            <div class="flex gap-3 w-[80%]">
                <Input class="w-[50%]" placeholder="Pesquisar ..." v-model="searchTerm" />
                <div class="w-[25%]">
                    <Select>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Tipo" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Fruits</SelectLabel>
                                <SelectItem value="apple">
                                    Apple
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div class="w-[25%]">
                    <Select>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Fruits</SelectLabel>
                                <SelectItem value="apple">
                                    Apple
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <div>
                <Button @click="emit('create')" variant="default">Novo Veículo</Button>
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
