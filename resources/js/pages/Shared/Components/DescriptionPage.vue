<script setup lang="ts">
interface DescriptionPageProps {
    title: string;
    description?: string;
}

defineProps<DescriptionPageProps>();
</script>

<template>
    <div class="flex flex-col gap-2 pt-4 pl-4">
        <h2 class="text-xl font-bold">{{ title }}</h2>
        <small v-if="description">{{ description }}</small>
    </div>
</template>