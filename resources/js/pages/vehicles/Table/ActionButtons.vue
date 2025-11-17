<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Edit, Trash, View, History } from 'lucide-vue-next';
import { VehiclesInterface } from '../types';
import { inject } from 'vue';

defineProps<{ vehicle: VehiclesInterface }>();

const onEditVehicle = inject<(vehicle: VehiclesInterface) => void>('onEditVehicle');
const onViewVehicle = inject<(vehicle: VehiclesInterface) => void>('onViewVehicle');
const onDeleteVehicle = inject<(vehicle: VehiclesInterface) => void>('onDeleteVehicle');

</script>
<template>
    <div class="flex gap-2">
        <Button size="icon" variant="outline" @click="onViewVehicle && onViewVehicle(vehicle)">
            <View class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="outline" @click="onEditVehicle && onEditVehicle(vehicle)">
            <Edit class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="outline">
            <History class="h-4 w-4" />
        </Button>
    </div>
</template>