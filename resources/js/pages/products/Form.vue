<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { Money3Component } from 'v-money3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import ProductSuppliers from './ProductSuppliers.vue';
import type { ProductInterface } from './types';

interface Props {
    product?: ProductInterface | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    submitted: [data: { mode: 'create' | 'edit'; data: any }];
}>();

const mode = ref<'create' | 'edit'>('create');

const categories = [
    { value: 'engine', label: 'Motor' },
    { value: 'suspension', label: 'Suspensão' },
    { value: 'brakes', label: 'Freios' },
    { value: 'electrical', label: 'Elétrica' },
    { value: 'body', label: 'Carroceria' },
    { value: 'body_parts', label: 'Peças de Carroceria' },
    { value: 'transmission', label: 'Transmissão' },
    { value: 'tires', label: 'Pneus' },
    { value: 'lubricants', label: 'Lubrificantes' },
    { value: 'fluids', label: 'Fluidos' },
    { value: 'filters', label: 'Filtros' },
    { value: 'accessories', label: 'Acessórios' },
    { value: 'interior', label: 'Interior' },
    { value: 'tools', label: 'Ferramentas' },
    { value: 'other', label: 'Outros' },
];

const units = [
    { value: 'unit', label: 'Unidade' },
    { value: 'liter', label: 'Litro' },
    { value: 'kg', label: 'Quilograma' },
    { value: 'meter', label: 'Metro' },
    { value: 'box', label: 'Caixa' },
];

const formSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Nome é obrigatório').max(255),
        description: z.string().optional(),
        sku: z.string().min(1, 'SKU é obrigatório').max(50),
        barcode: z.string().max(50).optional(),
        manufacturer: z.string().max(100).optional(),
        category: z.enum([
            'engine',
            'suspension',
            'brakes',
            'electrical',
            'body',
            'transmission',
            'tires',
            'lubricants',
            'filters',
            'accessories',
            'other',
            'fluids',
            'body_parts',
            'interior',
            'tools',
        ]),
        unit: z.enum(['unit', 'liter', 'kg', 'meter', 'box']),
        stock_quantity: z.number().int().min(0, 'Quantidade deve ser maior ou igual a 0'),
        min_stock_level: z.number().int().min(0, 'Estoque mínimo deve ser maior ou igual a 0'),
        unit_price: z.number().min(0, 'Preço deve ser maior ou igual a 0'),
        suggested_price: z.number().min(0, 'Preço sugerido deve ser maior ou igual a 0').optional(),
        is_active: z.boolean().default(true),
    })
);

const { handleSubmit, defineField, values, setValues, resetForm } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: '',
        description: '',
        sku: '',
        barcode: '',
        manufacturer: '',
        category: 'other' as const,
        unit: 'unit' as const,
        stock_quantity: 0,
        min_stock_level: 0,
        unit_price: 0,
        suggested_price: 0,
        is_active: true,
    },
});

const [name, nameAttrs] = defineField('name');
const [description, descriptionAttrs] = defineField('description');
const [sku, skuAttrs] = defineField('sku');
const [barcode, barcodeAttrs] = defineField('barcode');
const [manufacturer, manufacturerAttrs] = defineField('manufacturer');
const [category, categoryAttrs] = defineField('category');
const [unit, unitAttrs] = defineField('unit');
const [stock_quantity, stockQuantityAttrs] = defineField('stock_quantity');
const [min_stock_level, minStockLevelAttrs] = defineField('min_stock_level');
const [unit_price, unitPriceAttrs] = defineField('unit_price');
const [suggested_price, suggestedPriceAttrs] = defineField('suggested_price');
const [is_active] = defineField('is_active');

// is_active needs special handling for Switch component
const isActiveModel = computed({
    get: () => is_active.value,
    set: (val) => { is_active.value = val; }
});

// Computed values for Money3Component to ensure they're never undefined
const unitPriceModel = computed({
    get: () => unit_price.value || 0,
    set: (val) => { unit_price.value = val; }
});

const suggestedPriceModel = computed({
    get: () => suggested_price.value || 0,
    set: (val) => { suggested_price.value = val; }
});

// Configuração para formatação monetária brasileira
const moneyConfig = {
    decimal: ',',
    thousands: '.',
    prefix: 'R$ ',
    precision: 2,
    masked: false,
};

onMounted(() => {
    if (props.product) {
        mode.value = 'edit';
        fillValues();
    }
});

function fillValues() {
    if (props.product) {
        setValues({
            name: props.product.name,
            description: props.product.description || '',
            sku: props.product.sku,
            barcode: props.product.barcode || '',
            manufacturer: props.product.manufacturer || '',
            category: props.product.category,
            unit: props.product.unit,
            stock_quantity: Number(props.product.stock_quantity),
            min_stock_level: Number(props.product.min_stock_level),
            unit_price: Number(props.product.unit_price),
            suggested_price: Number(props.product.suggested_price || 0),
            is_active: props.product.is_active,
        });
    }
}

const onSubmit = handleSubmit((values) => {
    emit('submitted', { mode: mode.value, data: values });
});

function clear() {
    resetForm();
    mode.value = 'create';
}

defineExpose({ clear });

const isEditing = computed(() => !!props.product);
</script>

<template>
    <form @submit="onSubmit" class="space-y-6">
        <Tabs default-value="general" class="w-full">
            <TabsList class="grid w-full grid-cols-2">
                <TabsTrigger value="general">Dados Gerais</TabsTrigger>
                <TabsTrigger value="suppliers">Fornecedores</TabsTrigger>
            </TabsList>

            <TabsContent value="general" class="space-y-4 pt-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name">Nome *</Label>
                        <Input
                            id="name"
                            v-model="name"
                            v-bind="nameAttrs"
                            placeholder="Nome do produto"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="sku">SKU *</Label>
                        <Input
                            id="sku"
                            v-model="sku"
                            v-bind="skuAttrs"
                            placeholder="Código SKU"
                            maxlength="50"
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="description">Descrição</Label>
                    <Textarea
                        id="description"
                        v-model="description"
                        v-bind="descriptionAttrs"
                        placeholder="Descrição detalhada do produto"
                        rows="3"
                    />
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="barcode">Código de Barras</Label>
                        <Input
                            id="barcode"
                            v-model="barcode"
                            v-bind="barcodeAttrs"
                            placeholder="EAN/GTIN"
                            maxlength="50"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="manufacturer">Fabricante</Label>
                        <Input
                            id="manufacturer"
                            v-model="manufacturer"
                            v-bind="manufacturerAttrs"
                            placeholder="Nome do fabricante"
                            maxlength="100"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="category">Categoria *</Label>
                        <Select v-model="category" v-bind="categoryAttrs">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Selecione" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="cat in categories"
                                    :key="cat.value"
                                    :value="cat.value"
                                >
                                    {{ cat.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="unit">Unidade *</Label>
                        <Select v-model="unit" v-bind="unitAttrs">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="u in units"
                                    :key="u.value"
                                    :value="u.value"
                                >
                                    {{ u.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <Label for="stock_quantity">Quantidade em Estoque *</Label>
                        <Input
                            id="stock_quantity"
                            v-model.number="stock_quantity"
                            v-bind="stockQuantityAttrs"
                            type="number"
                            min="0"
                            step="1"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="min_stock_level">Estoque Mínimo *</Label>
                        <Input
                            id="min_stock_level"
                            v-model.number="min_stock_level"
                            v-bind="minStockLevelAttrs"
                            type="number"
                            min="0"
                            step="1"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="unit_price">Preço Unitário *</Label>
                        <Money3Component
                            v-model.number="unitPriceModel"
                            v-bind="moneyConfig"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="suggested_price">Preço Sugerido</Label>
                        <Money3Component
                            v-model.number="suggestedPriceModel"
                            v-bind="moneyConfig"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <Switch
                        id="is_active"
                        v-model="isActiveModel"
                    />
                    <Label for="is_active" class="cursor-pointer">Produto ativo</Label>
                </div>
            </TabsContent>

            <TabsContent value="suppliers" class="space-y-4 pt-4">
                <div v-if="!isEditing" class="text-sm text-muted-foreground">
                    A gestão de fornecedores estará disponível após salvar o produto.
                </div>
                <ProductSuppliers v-else :product-id="product!.id!" />
            </TabsContent>
        </Tabs>

        <div class="flex justify-end gap-2">
            <Button type="submit">
                {{ isEditing ? 'Atualizar' : 'Criar' }} Produto
            </Button>
        </div>
    </form>
</template>
