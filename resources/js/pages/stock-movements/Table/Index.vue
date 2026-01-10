<script setup lang="ts">
import { computed } from 'vue';
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { columns } from './columns';
import { useStockMovements } from '../composables/useStockMovements';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import ProductCombobox from '@/components/ProductCombobox.vue';
import DatePicker from '@/components/DatePicker.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FilterX } from 'lucide-vue-next';

const {
    movementsData,
    totalItems,
    currentPage,
    pageSize,
    isLoading,
    sorting,
    filters,
    goToNextPage,
    goToPreviousPage,
    updateFilters,
    clearFilters,
} = useStockMovements();

const table = useVueTable({
    get data() {
        return movementsData.value;
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    manualSorting: true,
    state: {
        get sorting() {
            return sorting.value;
        },
    },
    onSortingChange: (updaterOrValue) => {
        sorting.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(sorting.value)
                : updaterOrValue;
    },
    pageCount: computed(() => Math.ceil(totalItems.value / pageSize.value)).value,
});

const totalPages = computed(() => Math.ceil(totalItems.value / pageSize.value));
const startItem = computed(() => (currentPage.value - 1) * pageSize.value + 1);
const endItem = computed(() =>
    Math.min(currentPage.value * pageSize.value, totalItems.value)
);

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some((v) => v !== undefined && v !== '');
});
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle>Filtros</CardTitle>
                    <Button
                        v-if="hasActiveFilters"
                        variant="outline"
                        size="sm"
                        @click="clearFilters"
                    >
                        <FilterX class="mr-2 h-4 w-4" />
                        Limpar Filtros
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-5 gap-4">
                    <div class="space-y-2">
                        <Label>Produto</Label>
                        <ProductCombobox
                            :model-value="filters.product_id"
                            @update:model-value="(val) => updateFilters({ product_id: val })"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label>Tipo de Movimento</Label>
                        <Select
                            :model-value="filters.movement_type || 'all'"
                            @update:model-value="(val) => updateFilters({ movement_type: val === 'all' ? undefined : val as any })"
                        >
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Todos" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todos</SelectItem>
                                <SelectItem value="in">Entrada</SelectItem>
                                <SelectItem value="out">Saída</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <Label>Motivo</Label>
                        <Select
                            :model-value="filters.reason || 'all'"
                            @update:model-value="(val) => updateFilters({ reason: val === 'all' ? undefined : val as any })"
                        >
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Todos" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todos</SelectItem>
                                <SelectItem value="purchase">Compra</SelectItem>
                                <SelectItem value="sale">Venda</SelectItem>
                                <SelectItem value="adjustment">Ajuste</SelectItem>
                                <SelectItem value="loss">Perda/Quebra</SelectItem>
                                <SelectItem value="return">Devolução</SelectItem>
                                <SelectItem value="transfer">Transferência</SelectItem>
                                <SelectItem value="initial">Estoque Inicial</SelectItem>
                                <SelectItem value="other">Outro</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <Label>Data Início</Label>
                        <DatePicker
                            :model-value="filters.date_from"
                            @update:model-value="(val) => updateFilters({ date_from: val })"
                            placeholder="Selecione a data inicial"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label>Data Fim</Label>
                        <DatePicker
                            :model-value="filters.date_to"
                            @update:model-value="(val) => updateFilters({ date_to: val })"
                            placeholder="Selecione a data final"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="isLoading">
                        <TableRow>
                            <TableCell
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                Carregando...
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else-if="table.getRowModel().rows?.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                Nenhuma movimentação encontrada.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ startItem }} a {{ endItem }} de {{ totalItems }} movimentações
            </div>
            <div class="flex items-center space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="currentPage === 1"
                    @click="goToPreviousPage"
                >
                    Anterior
                </Button>
                <div class="text-sm">
                    Página {{ currentPage }} de {{ totalPages }}
                </div>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="currentPage >= totalPages"
                    @click="goToNextPage"
                >
                    Próxima
                </Button>
            </div>
        </div>
    </div>
</template>
