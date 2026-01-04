<script setup lang="ts">
import { ChevronRight } from 'lucide-vue-next'
import { CalendarNext, type CalendarNextProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarNextProps>()
</script>

<template>
  <CalendarNext
    :class="cn(
      'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
      'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
    )"
  >
    <slot>
      <ChevronRight class="h-4 w-4" />
    </slot>
  </CalendarNext>
</template>
