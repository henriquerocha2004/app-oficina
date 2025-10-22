<script setup lang="ts">
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import Form from './Form.vue';
import { ClientsApi } from '@/api/Clients';
import { ref } from 'vue';
import { toast } from 'vue-sonner';


export interface CreateClientProps {
    show: boolean;
}
const props = defineProps<CreateClientProps>();
const emit = defineEmits(['created', 'update:show']);
const formComponent = ref<InstanceType<typeof Form> | null>(null);

async function save(formData: any) {
    if (formData.mode !== 'create') return;
    const response = await ClientsApi.save(formData.data);

    if (response.status === 'error') {
        toast.error('Erro ao criar cliente', { position: 'top-right' });
        return;
    }

    emit('created');
    emit('update:show', false);
    formComponent.value?.clear();
    toast.success('Cliente criado com sucesso', { position: 'top-right' });
}

</script>
<template>
    <Sheet :open="props.show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent>
            <SheetHeader>
                <SheetTitle>Novo Cliente</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" ref="formComponent" />
            </div>
        </SheetContent>
    </Sheet>
</template>
