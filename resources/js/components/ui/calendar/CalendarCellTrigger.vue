<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { CalendarCellTrigger, type CalendarCellTriggerProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarCellTriggerProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <CalendarCellTrigger
    :day="props.day"
    :month="props.month"
    :class="cn(
      'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
      'h-9 w-9 p-0 font-normal aria-selected:opacity-100',
      'hover:bg-accent hover:text-accent-foreground',
      'data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[selected]:hover:bg-primary data-[selected]:hover:text-primary-foreground data-[selected]:focus:bg-primary data-[selected]:focus:text-primary-foreground',
      'data-[today]:bg-accent data-[today]:text-accent-foreground',
      'data-[outside-month]:text-muted-foreground data-[outside-month]:opacity-50 data-[outside-month]:pointer-events-none',
      'data-[disabled]:text-muted-foreground data-[disabled]:opacity-50',
      props.class,
    )"
  >
    <slot />
  </CalendarCellTrigger>
</template>
