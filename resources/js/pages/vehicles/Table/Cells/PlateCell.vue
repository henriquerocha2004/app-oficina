<script setup lang="ts">
import { VehiclesInterface } from '../../types';

defineProps<{ vehicle: VehiclesInterface }>();

</script>

<template>
  <div class="flex flex-row items-center space-x-2 p-2">
    <span class="text-sm text-white dark:text-gray-300 bg-gray-500 rounded p-1">{{ vehicle.licensePlate }}</span>
  </div>
</template>