<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Edit, Trash } from 'lucide-vue-next';
import type { SubscriptionPlan } from '../types';
import { inject } from 'vue';

defineProps<{ plan: SubscriptionPlan }>();

const onEditPlan = inject<(plan: SubscriptionPlan) => void>('onEditPlan');
const onDeletePlan = inject<(plan: SubscriptionPlan) => void>('onDeletePlan');
</script>

<template>
    <div class="flex gap-2">
        <Button size="icon" variant="outline" @click="onEditPlan && onEditPlan(plan)">
            <Edit class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="outline" @click="onDeletePlan && onDeletePlan(plan)">
            <Trash class="h-4 w-4" />
        </Button>
    </div>
</template>
