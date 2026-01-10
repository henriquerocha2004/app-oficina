<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import type { DateValue } from '@internationalized/date'
import { CalendarCell, type CalendarCellProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarCellProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <CalendarCell
    :date="props.date"
    :class="cn('relative h-9 w-9 p-0 text-center text-sm focus-within:relative focus-within:z-20 [&:has([data-selected])]:bg-accent [&:has([data-selected][data-outside-month])]:bg-accent/50', props.class)"
  >
    <slot />
  </CalendarCell>
</template>
