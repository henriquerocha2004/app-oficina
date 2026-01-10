<script setup lang="ts">
import { VehiclesInterface } from '../../types';
import { CarIcon, BikeIcon } from 'lucide-vue-next';

defineProps<{ vehicle: VehiclesInterface }>();

</script>
<template>
  <div class="flex flex-row items-center space-x-2 p-2">
    <component :is="vehicle.vehicle_type === 'car' ? CarIcon : BikeIcon"
      class="w-5 h-5 text-gray-500 dark:text-white" />
    <span class="text-sm text-gray-700 dark:text-gray-300">{{ vehicle.model }}</span>
  </div>
</template>