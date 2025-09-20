<script setup lang="ts">
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import Form from './Form.vue';
import { ClientInterface } from './client';

export interface UpdateClientProps {
    show: boolean;
    clientData?: ClientInterface | null;
}
const props = defineProps<UpdateClientProps>();

function save(formData: any) {
    if (formData.mode !== 'edit') return;
    console.log('Atualizar cliente existente', formData.data);
}

</script>
<template>
    <Sheet :open="props.show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent>
            <SheetHeader>
                <SheetTitle>Editar: {{ props.clientData?.name }}</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" :client="props.clientData" />
            </div>
        </SheetContent>
    </Sheet>
</template>