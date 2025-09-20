<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Edit, Trash, View } from 'lucide-vue-next';
import { ClientInterface } from '../client';
import { inject } from 'vue';

defineProps<{ client: ClientInterface }>();

const onEditClient = inject<(client: ClientInterface) => void>('onEditClient');
const onViewClient = inject<(client: ClientInterface) => void>('onViewClient');

</script>
<template>
    <div class="flex gap-2">
        <Button size="icon" variant="outline" @click="onViewClient && onViewClient(client)">
            <View class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="outline" @click="onEditClient && onEditClient(client)">
            <Edit class="h-4 w-4" />
        </Button>
        <Button size="icon" variant="outline" @click="console.log('deletar')">
            <Trash class="h-4 w-4" />
        </Button>
    </div>
</template>