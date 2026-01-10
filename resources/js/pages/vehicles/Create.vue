<script setup lang="ts">
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import Form from './Form.vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { VehiclesApi } from '@/api/Vehicles';

export interface CreateVehicleProps {
    show: boolean;
}

const props = defineProps<CreateVehicleProps>();
const emit = defineEmits(['created', 'update:show']);
const formComponent = ref<InstanceType<typeof Form> | null>(null);

async function save(formData: any) {
    console.log(formData);
    if (formData.mode !== 'create') return;
    try {
        const response = await VehiclesApi.save(formData.data);

        if (response.status === 'error') {
            toast.error('Erro ao salvar o veiculo', { position: 'top-right' });
            return;
        }

        emit('created');
        emit('update:show', false);
        formComponent.value?.clear();
        toast.success('Veículo cadastrado com sucesso', { position: 'top-right' });

    } catch (error) {
        console.error('Erro ao criar veículo:', error);
        toast.error('Erro ao cadastrar veículo', { position: 'top-right' });
    }
}
</script>

<template>
    <Sheet :open="props.show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent class="w-[600px] sm:max-w-[600px]">
            <SheetHeader>
                <SheetTitle>Novo Veículo</SheetTitle>
            </SheetHeader>
            <div class="p-3 max-h-[calc(100vh-100px)] overflow-y-auto">
                <Form @submitted="save" ref="formComponent" />
            </div>
        </SheetContent>
    </Sheet>
</template>