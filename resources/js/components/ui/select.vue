<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface SelectProps {
    modelValue?: string | number;
    class?: string;
}

const props = defineProps<SelectProps>();
const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val as string),
});
</script>

<template>
    <select v-model="value" :class="cn(
        'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        $props.class
    )" v-bind="$attrs">
        <slot />
    </select>
</template>
