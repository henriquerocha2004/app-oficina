<script setup lang="ts">
import { computed } from 'vue';
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { columns } from './columns';
import { useSuppliers } from '../composables/useSuppliers';
import { SuppliersApi } from '@/api/Suppliers';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { toast } from '@/components/ui/toast';
import { Plus, MoreHorizontal, Pencil, Trash2, Info } from 'lucide-vue-next';
import type { SupplierInterface } from '../types';
import { ref } from 'vue';

const emit = defineEmits<{
    create: [];
    edit: [supplier: SupplierInterface];
    view: [supplier: SupplierInterface];
}>();

const {
    suppliersData,
    totalItems,
    currentPage,
    pageSize,
    searchTerm,
    sorting,
    isLoading,
    fetchSuppliers,
    goToNextPage,
    goToPreviousPage,
} = useSuppliers();

const showDeleteDialog = ref(false);
const supplierToDelete = ref<SupplierInterface | null>(null);

const table = useVueTable({
    get data() {
        return suppliersData.value;
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

const handleDelete = (supplier: SupplierInterface) => {
    supplierToDelete.value = supplier;
    showDeleteDialog.value = true;
};

const confirmDelete = async () => {
    if (!supplierToDelete.value) return;

    try {
        await SuppliersApi.delete(supplierToDelete.value.id);
        toast.success('Fornecedor excluído com sucesso');
        fetchSuppliers();
    } catch (error) {
        toast.error('Não foi possível excluir o fornecedor');
    } finally {
        showDeleteDialog.value = false;
        supplierToDelete.value = null;
    }
};

const refresh = fetchSuppliers;

defineExpose({ refresh });
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between gap-4">
            <Input
                v-model="searchTerm"
                placeholder="Buscar fornecedores..."
                class="max-w-sm"
            />
            <Button @click="emit('create')">
                <Plus class="mr-2 h-4 w-4" />
                Novo Fornecedor
            </Button>
        </div>

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
                        <TableHead class="w-[70px]">Ações</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="isLoading">
                        <TableRow>
                            <TableCell
                                :colspan="columns.length + 1"
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
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>Ações</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            @click="emit('view', row.original)"
                                        >
                                            <Info class="mr-2 h-4 w-4" />
                                            Visualizar
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            @click="emit('edit', row.original)"
                                        >
                                            <Pencil class="mr-2 h-4 w-4" />
                                            Editar
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            @click="handleDelete(row.original)"
                                            class="text-destructive"
                                        >
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Excluir
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colspan="columns.length + 1"
                                class="h-24 text-center"
                            >
                                Nenhum fornecedor encontrado.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ startItem }} a {{ endItem }} de {{ totalItems }} fornecedores
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

        <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Confirmar exclusão</AlertDialogTitle>
                    <AlertDialogDescription>
                        Tem certeza que deseja excluir o fornecedor
                        <strong>{{ supplierToDelete?.name }}</strong>?
                        Esta ação não pode ser desfeita.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction @click="confirmDelete">
                        Excluir
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
