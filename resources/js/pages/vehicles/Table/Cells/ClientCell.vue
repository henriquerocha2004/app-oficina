<script setup lang="ts">
import { VehiclesInterface } from '../../types';
import formatPhone from '@/utils/formatPhone';

defineProps<{ vehicle: VehiclesInterface }>();

</script>

<template>
  <div class="flex flex-col">
    <span class="text-sm">{{ vehicle.client || "-" }}</span>
    <span class="text-sm">{{ formatPhone(vehicle.phone || "-") }}</span>
  </div>
</template>