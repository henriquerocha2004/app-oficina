<script setup lang="ts">
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import Form from './Form.vue';

export interface CreateClientProps {
    show: boolean;
}
const props = defineProps<CreateClientProps>();

function save(formData: any) {
    if (formData.mode !== 'create') return;
    console.log('Salvar novo cliente', formData.data);
}

</script>
<template>
    <Sheet :open="props.show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent>
            <SheetHeader>
                <SheetTitle>Novo Cliente</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" />
            </div>
        </SheetContent>
    </Sheet>
</template>
