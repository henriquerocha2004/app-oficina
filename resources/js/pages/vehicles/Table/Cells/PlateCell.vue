<script setup lang="ts">
import { VehiclesInterface } from '../../types';

defineProps<{ vehicle: VehiclesInterface }>();
</script>

<template>
  <div v-if="vehicle.plate" class="p-2">
    <div class="
        w-24 h-12 
        bg-white
        border-2 border-blue-700
        rounded-md
        overflow-hidden
        shadow-md
        select-none
      ">
      <!-- Faixa azul superior -->
      <div class="bg-blue-700 text-white text-center py-0.5 text-[7px] font-semibold tracking-wide">
        BRASIL
      </div>

      <!-- Placa -->
      <div class="flex justify-center items-center py-1">
        <span class="font-black tracking-widest text-gray-900 uppercase">
          {{ vehicle.plate }}
        </span>
      </div>
    </div>
  </div>

  <div v-else class="flex flex-row items-center space-x-2 p-2">
    -
  </div>
</template>
