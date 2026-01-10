<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
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
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { toast } from '@/components/ui/toast';
import { ProductsApi } from '@/api/Products';
import { AlertTriangle } from 'lucide-vue-next';
import type { ProductInterface } from './types';

interface Props {
    show: boolean;
    productData?: ProductInterface | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:show': [value: boolean];
    adjusted: [];
}>();

const reasons = {
    in: [
        { value: 'purchase', label: 'Compra' },
        { value: 'return', label: 'Devolução de Cliente' },
        { value: 'transfer', label: 'Transferência (Entrada)' },
        { value: 'adjustment', label: 'Ajuste de Inventário' },
        { value: 'initial', label: 'Estoque Inicial' },
        { value: 'other', label: 'Outro' },
    ],
    out: [
        { value: 'sale', label: 'Venda' },
        { value: 'loss', label: 'Perda/Quebra' },
        { value: 'return', label: 'Devolução ao Fornecedor' },
        { value: 'transfer', label: 'Transferência (Saída)' },
        { value: 'adjustment', label: 'Ajuste de Inventário' },
        { value: 'other', label: 'Outro' },
    ],
};

const formSchema = toTypedSchema(
    z.object({
        movement_type: z.enum(['in', 'out']),
        quantity: z.number().int().min(1, 'Quantidade deve ser maior que 0'),
        reason: z.enum([
            'purchase',
            'sale',
            'adjustment',
            'loss',
            'return',
            'transfer',
            'initial',
            'other',
        ]),
        notes: z.string().optional(),
    })
);

const { handleSubmit, defineField, values, resetForm } = useForm({
    validationSchema: formSchema,
    initialValues: {
        movement_type: 'in' as const,
        quantity: 1,
        reason: 'purchase' as const,
        notes: '',
    },
});

const [movement_type, movementTypeAttrs] = defineField('movement_type');
const [quantity, quantityAttrs] = defineField('quantity');
const [reason, reasonAttrs] = defineField('reason');
const [notes, notesAttrs] = defineField('notes');

const isSubmitting = ref(false);

const availableReasons = computed(() => {
    return reasons[values.movement_type as 'in' | 'out'] || [];
});

const showInsufficientStockWarning = computed(() => {
    return (
        values.movement_type === 'out' &&
        values.quantity > (props.productData?.stock_quantity || 0)
    );
});

const newStockQuantity = computed(() => {
    const currentStock = props.productData?.stock_quantity || 0;
    if (values.movement_type === 'in') {
        return currentStock + (values.quantity || 0);
    }
    return currentStock - (values.quantity || 0);
});

const onSubmit = handleSubmit(async (formValues) => {
    if (showInsufficientStockWarning.value) {
        toast.error('Quantidade insuficiente em estoque');
        return;
    }

    if (!props.productData) return;

    isSubmitting.value = true;
    try {
        await ProductsApi.adjustStock(props.productData.id, {
            movement_type: formValues.movement_type,
            quantity: formValues.quantity,
            reason: formValues.reason,
            notes: formValues.notes,
        });
        toast.success('Estoque ajustado com sucesso');
        resetForm();
        emit('adjusted');
        emit('update:show', false);
    } catch (error: any) {
        toast.error(error.message || 'Não foi possível ajustar o estoque');
    } finally {
        isSubmitting.value = false;
    }
});

const handleClose = () => {
    resetForm();
    emit('update:show', false);
};
</script>

<template>
    <Sheet :open="show" @update:open="(val) => !val && handleClose()">
        <SheetContent class="overflow-y-auto sm:max-w-xl">
            <SheetHeader>
                <SheetTitle>Ajustar Estoque</SheetTitle>
            </SheetHeader>

            <div class="p-3 space-y-4">
                <div class="p-4 bg-muted rounded-md">
                    <p class="text-sm font-medium">{{ productData?.name }}</p>
                    <p class="text-sm text-muted-foreground">
                        Estoque atual: <span class="font-semibold">{{ productData?.stock_quantity }}</span>
                    </p>
                </div>

                <form @submit="onSubmit" class="space-y-4">
                    <div class="space-y-3">
                        <Label>Tipo de Movimento *</Label>
                        <RadioGroup v-model="movement_type" v-bind="movementTypeAttrs">
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem value="in" id="in" />
                                <Label for="in" class="cursor-pointer">Entrada</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <RadioGroupItem value="out" id="out" />
                                <Label for="out" class="cursor-pointer">Saída</Label>
                            </div>
                        </RadioGroup>
                    </div>

                    <div class="space-y-3">
                        <Label for="quantity">Quantidade *</Label>
                        <Input
                            id="quantity"
                            v-model.number="quantity"
                            v-bind="quantityAttrs"
                            type="number"
                            min="1"
                            step="1"
                        />
                    </div>

                    <div class="space-y-3">
                        <Label for="reason">Motivo *</Label>
                        <Select v-model="reason" v-bind="reasonAttrs">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o motivo" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="r in availableReasons"
                                    :key="r.value"
                                    :value="r.value"
                                >
                                    {{ r.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-3">
                        <Label for="notes">Observações</Label>
                        <Textarea
                            id="notes"
                            v-model="notes"
                            v-bind="notesAttrs"
                            placeholder="Informações adicionais (opcional)"
                            rows="3"
                        />
                    </div>

                    <Alert v-if="showInsufficientStockWarning" variant="destructive">
                        <AlertTriangle class="h-4 w-4" />
                        <AlertDescription>
                            Estoque insuficiente! Disponível: {{ productData?.stock_quantity }}
                        </AlertDescription>
                    </Alert>

                    <div v-else class="p-3 bg-muted rounded-md">
                        <p class="text-sm">
                            Novo estoque:
                            <span
                                class="font-semibold"
                                :class="{
                                    'text-green-600': movement_type === 'in',
                                    'text-red-600': movement_type === 'out',
                                }"
                            >
                                {{ newStockQuantity }}
                            </span>
                        </p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button
                            type="submit"
                            :disabled="isSubmitting || showInsufficientStockWarning"
                        >
                            {{ isSubmitting ? 'Ajustando...' : 'Confirmar' }}
                        </Button>
                    </div>
                </form>
            </div>
        </SheetContent>
    </Sheet>
</template>
