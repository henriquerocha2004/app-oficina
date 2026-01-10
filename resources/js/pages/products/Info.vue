<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import type { ProductInterface } from './types';

interface Props {
    show: boolean;
    product: ProductInterface;
}

defineProps<Props>();
const emit = defineEmits<{
    close: [];
}>();

const categoryLabels: Record<string, string> = {
    engine: 'Motor',
    suspension: 'Suspensão',
    brakes: 'Freios',
    electrical: 'Elétrica',
    body: 'Carroceria',
    transmission: 'Transmissão',
    tires: 'Pneus',
    lubricants: 'Lubrificantes',
    filters: 'Filtros',
    accessories: 'Acessórios',
    other: 'Outros',
};

const unitLabels: Record<string, string> = {
    unit: 'Unidade',
    liter: 'Litro',
    kg: 'Quilograma',
    meter: 'Metro',
    box: 'Caixa',
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

const getStockBadgeVariant = (product: ProductInterface) => {
    if (product.stock_quantity <= product.min_stock_level) return 'destructive';
    if (product.stock_quantity <= product.min_stock_level * 1.5) return 'warning';
    return 'default';
};
</script>

<template>
    <Dialog :open="show" @update:open="(val) => !val && emit('close')">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>Informações do Produto</DialogTitle>
            </DialogHeader>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ product.name }}</h3>
                    <p v-if="product.description" class="text-sm text-muted-foreground mt-1">
                        {{ product.description }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <Badge :variant="product.is_active ? 'default' : 'secondary'">
                        {{ product.is_active ? 'Ativo' : 'Inativo' }}
                    </Badge>
                    <Badge :variant="getStockBadgeVariant(product)">
                        Estoque: {{ product.stock_quantity }} {{ unitLabels[product.unit] }}
                    </Badge>
                    <Badge v-if="product.is_low_stock" variant="destructive">
                        Estoque Baixo
                    </Badge>
                </div>

                <Separator />

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">SKU</p>
                        <p class="text-sm">{{ product.sku }}</p>
                    </div>

                    <div v-if="product.barcode">
                        <p class="text-sm font-medium text-muted-foreground">
                            Código de Barras
                        </p>
                        <p class="text-sm">{{ product.barcode }}</p>
                    </div>

                    <div v-if="product.manufacturer">
                        <p class="text-sm font-medium text-muted-foreground">Fabricante</p>
                        <p class="text-sm">{{ product.manufacturer }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Categoria</p>
                        <p class="text-sm">{{ categoryLabels[product.category] }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Unidade de Medida
                        </p>
                        <p class="text-sm">{{ unitLabels[product.unit] }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Estoque Mínimo
                        </p>
                        <p class="text-sm">{{ product.min_stock_level }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Preço Unitário
                        </p>
                        <p class="text-sm">{{ formatCurrency(product.unit_price) }}</p>
                    </div>

                    <div v-if="product.suggested_price">
                        <p class="text-sm font-medium text-muted-foreground">
                            Preço Sugerido
                        </p>
                        <p class="text-sm">
                            {{ formatCurrency(product.suggested_price) }}
                        </p>
                    </div>
                </div>

                <div v-if="product.suppliers && product.suppliers.length > 0">
                    <Separator class="my-4" />
                    <h4 class="text-sm font-semibold mb-2">Fornecedores</h4>
                    <div class="space-y-2">
                        <div
                            v-for="supplier in product.suppliers"
                            :key="supplier.id"
                            class="text-sm p-2 bg-muted rounded-md"
                        >
                            <p class="font-medium">{{ supplier.name }}</p>
                            <p v-if="supplier.pivot?.supplier_sku" class="text-muted-foreground">
                                SKU Fornecedor: {{ supplier.pivot.supplier_sku }}
                            </p>
                            <p v-if="supplier.pivot?.cost_price" class="text-muted-foreground">
                                Custo: {{ formatCurrency(supplier.pivot.cost_price) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
