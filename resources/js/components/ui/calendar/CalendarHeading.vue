<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { CalendarHeading, type CalendarHeadingProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<CalendarHeadingProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <CalendarHeading
    :class="cn('text-sm font-medium', props.class)"
  >
    <slot />
  </CalendarHeading>
</template>
