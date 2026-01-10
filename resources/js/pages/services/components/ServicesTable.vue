<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Service } from '@/types';
import axios from 'axios';
import { columns } from './columns';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';

const services = ref<Service[]>([]);
const loading = ref(false);
const totalItems = ref(0);
const currentPage = ref(1);
const perPage = ref(10);
const searchTerm = ref('');

async function fetchServices() {
    loading.value = true;
    try {
        const response = await axios.get('/services/filters', {
            params: {
                search: searchTerm.value,
                page: currentPage.value,
                per_page: perPage.value,
            },
        });
        services.value = response.data.services.items;
        totalItems.value = response.data.services.total_items;
    } catch (error) {
        console.error('Error loading services:', error);
    } finally {
        loading.value = false;
    }
}

const table = useVueTable({
    get data() {
        return services.value;
    },
    get columns() {
        return columns;
    },
    getCoreRowModel: getCoreRowModel(),
});

onMounted(() => {
    fetchServices();
});

watch(searchTerm, () => {
    currentPage.value = 1;
    fetchServices();
});

function goToNextPage() {
    if (currentPage.value * perPage.value < totalItems.value) {
        currentPage.value++;
        fetchServices();
    }
}

function goToPreviousPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
        fetchServices();
    }
}

defineExpose({
    fetchServices,
});
</script>

<template>
    <div class="p-4 space-y-4">
        <div class="flex items-center gap-4">
            <Input v-model="searchTerm" placeholder="Buscar serviços..." class="max-w-sm" />
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
                    <template v-if="table.getRowModel().rows?.length && !loading">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() ? 'selected' : undefined">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else-if="loading">
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            Carregando...
                        </TableCell>
                    </TableRow>
                    <TableRow v-else>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            Nenhum serviço encontrado.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ (currentPage - 1) * perPage + 1 }} a
                {{ Math.min(currentPage * perPage, totalItems) }} de
                {{ totalItems }} resultados
            </div>
            <div class="flex gap-2">
                <Button variant="outline" size="sm" @click="goToPreviousPage" :disabled="currentPage === 1">
                    Anterior
                </Button>
                <Button variant="outline" size="sm" @click="goToNextPage"
                    :disabled="currentPage * perPage >= totalItems">
                    Próximo
                </Button>
            </div>
        </div>
    </div>
</template>
