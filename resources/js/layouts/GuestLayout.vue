<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import 'vue-sonner/style.css';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Toaster />
        <slot />
    </div>
</template>
