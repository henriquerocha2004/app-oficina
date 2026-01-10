<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    variant?: 'default' | 'secondary' | 'destructive' | 'outline';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});

const variantClasses = computed(() => {
    const variants = {
        default: 'border-transparent bg-primary text-primary-foreground',
        secondary: 'border-transparent bg-secondary text-secondary-foreground',
        destructive: 'border-transparent bg-destructive text-destructive-foreground',
        outline: 'text-foreground',
    };
    return variants[props.variant];
});
</script>

<template>
    <div
        :class="cn(
            'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
            variantClasses,
            props.class
        )"
    >
        <slot />
    </div>
</template>
