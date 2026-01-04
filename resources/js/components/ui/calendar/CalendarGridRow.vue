<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { CalendarGridRow, type CalendarGridRowProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarGridRowProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <CalendarGridRow :class="cn('flex', props.class)">
    <slot />
  </CalendarGridRow>
</template>
