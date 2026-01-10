<script setup lang="ts">
import { ref } from 'vue';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { toast } from '@/components/ui/toast';
import { ProductsApi } from '@/api/Products';
import Form from './Form.vue';
import type { ProductInterface } from './types';

interface Props {
    show: boolean;
    productData?: ProductInterface | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:show': [value: boolean];
    updated: [];
}>();

const formComponent = ref<InstanceType<typeof Form> | null>(null);

async function save(formData: any) {
    if (formData.mode !== 'edit') return;
    
    try {
        await ProductsApi.update(props.productData!.id, formData.data);
        emit('updated');
        emit('update:show', false);
        formComponent.value?.clear();
        toast.success('Produto atualizado com sucesso');
    } catch (error: any) {
        toast.error(error.message || 'Não foi possível atualizar o produto');
    }
}
</script>

<template>
    <Sheet :open="show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent class="overflow-y-auto sm:max-w-2xl">
            <SheetHeader>
                <SheetTitle>Editar: {{ productData?.name }}</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" :product="productData" ref="formComponent" />
            </div>
        </SheetContent>
    </Sheet>
</template>
