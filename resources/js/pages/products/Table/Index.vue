<script setup lang="ts">
import { computed } from 'vue';
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    type SortingState,
} from '@tanstack/vue-table';
import { columns } from './columns';
import { useProductsTable } from '../composables/useProductsTable';
import { ProductsApi } from '@/api/Products';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
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
import { Plus, MoreHorizontal, Pencil, Trash2, Info, PackagePlus } from 'lucide-vue-next';
import type { ProductInterface } from '../types';
import { ref } from 'vue';

const emit = defineEmits<{
    create: [];
    edit: [product: ProductInterface];
    view: [product: ProductInterface];
    adjustStock: [product: ProductInterface];
}>();

const {
    productsData,
    totalItems,
    currentPage,
    pageSize,
    searchTerm,
    sorting,
    filterLowStock,
    isLoading,
    goToNextPage,
    goToPreviousPage,
    refresh,
} = useProductsTable();

const showDeleteDialog = ref(false);
const productToDelete = ref<ProductInterface | null>(null);

const table = useVueTable({
    get data() {
        return productsData.value;
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

const handleDelete = (product: ProductInterface) => {
    productToDelete.value = product;
    showDeleteDialog.value = true;
};

const confirmDelete = async () => {
    if (!productToDelete.value) return;

    try {
        await ProductsApi.delete(productToDelete.value.id);
        toast.success('Produto excluído com sucesso');
        refresh();
    } catch (error) {
        toast.error('Não foi possível excluir o produto');
    } finally {
        showDeleteDialog.value = false;
        productToDelete.value = null;
    }
};

defineExpose({ refresh });
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1">
                <Input
                    v-model="searchTerm"
                    placeholder="Buscar produtos..."
                    class="max-w-sm"
                />
                <div class="flex items-center gap-2">
                    <Switch
                        v-model="filterLowStock"
                        id="low-stock"
                    />
                    <Label for="low-stock" class="cursor-pointer">
                        Estoque baixo
                    </Label>
                </div>
            </div>
            <Button @click="emit('create')">
                <Plus class="mr-2 h-4 w-4" />
                Novo Produto
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
                            :data-state="row.getIsSelected() ? 'selected' : undefined"
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
                                            @click="emit('adjustStock', row.original)"
                                        >
                                            <PackagePlus class="mr-2 h-4 w-4" />
                                            Ajustar Estoque
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
                                Nenhum produto encontrado.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ startItem }} a {{ endItem }} de {{ totalItems }} produtos
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
                        Tem certeza que deseja excluir o produto
                        <strong>{{ productToDelete?.name }}</strong>?
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
