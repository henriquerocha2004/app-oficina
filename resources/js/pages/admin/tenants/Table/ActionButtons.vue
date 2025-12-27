<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Edit, Trash } from 'lucide-vue-next';
import type { Tenant } from '../types';
import { inject } from 'vue';

defineProps<{ tenant: Tenant }>();

const onEditTenant = inject<(tenant: Tenant) => void>('onEditTenant');
const onDeleteTenant = inject<(tenant: Tenant) => void>('onDeleteTenant');
</script>

<template>
    <div class="flex gap-2">
        <Button size="icon" variant="outline" @click="onEditTenant && onEditTenant(tenant)">
            <Edit class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="outline" @click="onDeleteTenant && onDeleteTenant(tenant)">
            <Trash class="h-4 w-4" />
        </Button>
    </div>
</template>
