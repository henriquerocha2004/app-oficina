<script setup lang="ts">
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import Form from './Form.vue';
import { ClientInterface } from './types';
import { ClientsApi } from '@/api/Clients';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

export interface UpdateClientProps {
    show: boolean;
    clientData?: ClientInterface | null;
}
const props = defineProps<UpdateClientProps>();
const emit = defineEmits(['updated', 'update:show']);
const formComponent = ref<InstanceType<typeof Form> | null>(null);

async function save(formData: any) {
    if (formData.mode !== 'edit') return;
    formData.data.id = props.clientData?.id;
    const response = await ClientsApi.update(formData.data);

    if (response.status === 'error') {
        toast.error('Erro ao atualizar cliente', { position: 'top-right' });
        return;
    }

    emit('updated');
    emit('update:show', false);
    formComponent.value?.clear();
    toast.success('Cliente atualizado com sucesso', { position: 'top-right' });
}
</script>
<template>
    <Sheet :open="props.show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent>
            <SheetHeader>
                <SheetTitle>Editar: {{ props.clientData?.name }}</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" :client="props.clientData" ref="formComponent" />
            </div>
        </SheetContent>
    </Sheet>
</template>