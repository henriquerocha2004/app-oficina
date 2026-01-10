<script setup lang="ts">
import { ChevronLeft } from 'lucide-vue-next'
import { CalendarPrev, type CalendarPrevProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarPrevProps>()
</script>

<template>
  <CalendarPrev
    :class="cn(
      'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
      'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
    )"
  >
    <slot>
      <ChevronLeft class="h-4 w-4" />
    </slot>
  </CalendarPrev>
</template>
