<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { CalendarGrid, type CalendarGridProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarGridProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <CalendarGrid :class="cn('w-full border-collapse space-y-1', props.class)">
    <slot />
  </CalendarGrid>
</template>
