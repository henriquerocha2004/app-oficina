<script setup lang="ts">
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import Form from './Form.vue';
import { VehiclesInterface } from './types';
import { VehiclesApi } from '@/api/Vehicles';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

export interface UpdateVehicleProps {
    show: boolean;
    vehicleData?: VehiclesInterface | null;
}
const props = defineProps<UpdateVehicleProps>();
const emit = defineEmits(['updated', 'update:show']);
const formComponent = ref<InstanceType<typeof Form> | null>(null);

async function save(formData: any) {
    if (formData.mode !== 'edit') return;

    try {
        const vehicleToUpdate = {
            ...formData.data,
            id: props.vehicleData?.id,
        };

        const response = await VehiclesApi.update(vehicleToUpdate);

        if (response.status === 'error') {
            toast.error('Erro ao atualizar veículo', { position: 'top-right' });
            return;
        }

        emit('updated');
        emit('update:show', false);
        formComponent.value?.clear();
        toast.success('Veículo atualizado com sucesso', { position: 'top-right' });
    } catch (error) {
        toast.error('Erro ao atualizar veículo', { position: 'top-right' });
    }
}
</script>
<template>
    <Sheet :open="props.show" @update:open="(value) => $emit('update:show', value)">
        <SheetContent class="w-[600px] sm:max-w-[600px]">
            <SheetHeader>
                <SheetTitle>Editar: {{ props.vehicleData?.model }}</SheetTitle>
            </SheetHeader>
            <div class="p-3">
                <Form @submitted="save" :vehicle="props.vehicleData" ref="formComponent" />
            </div>
        </SheetContent>
    </Sheet>
</template>
