<script setup lang="ts">
import { ref } from 'vue';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { toast } from '@/components/ui/toast';
import { SuppliersApi } from '@/api/Suppliers';
import Form from './Form.vue';

interface Props {
    show: boolean;
}

defineProps<Props>();
const emit = defineEmits<{
    'update:show': [value: boolean];
    created: [];
}>();

const formComponent = ref<InstanceType<typeof Form> | null>(null);

async function save(formData: any) {
    if (formData.mode !== 'create') return;
    
    try {
        await SuppliersApi.save(formData.data);
        emit('created');
        emit('update:show', false);
        formComponent.value?.clear();
        toast.success('Fornecedor criado com sucesso');
    } catch (error: any) {
        toast.error(error.message || 'Não foi possível criar o fornecedor');
    }
}
</script>

<template>
    <Sheet :open="show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent class="overflow-y-auto sm:max-w-2xl">
            <SheetHeader>
                <SheetTitle>Novo Fornecedor</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" ref="formComponent" />
            </div>
        </SheetContent>
    </Sheet>
</template>
