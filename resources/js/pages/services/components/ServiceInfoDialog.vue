<script setup lang="ts">
import { Service } from '@/types';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps<{
    open: boolean;
    service: Service;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const categoryLabels: Record<string, string> = {
    maintenance: 'Manutenção',
    repair: 'Reparo',
    diagnostic: 'Diagnóstico',
    painting: 'Pintura',
    alignment: 'Alinhamento',
    other: 'Outro',
};

function formatCurrency(value: number) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('pt-BR');
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[500px]">
            <DialogHeader>
                <DialogTitle>Detalhes do Serviço</DialogTitle>
            </DialogHeader>
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold">{{ service.name }}</h3>
                    <span
                        :class="service.is_active
                            ? 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                            : 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                        class="mt-2">
                        {{ service.is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                <div v-if="service.description" class="space-y-1">
                    <p class="text-sm font-medium text-gray-500">Descrição</p>
                    <p class="text-sm">{{ service.description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-gray-500">Categoria</p>
                        <p class="text-sm">{{ categoryLabels[service.category] }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-medium text-gray-500">Preço Base</p>
                        <p class="text-sm font-semibold">{{ formatCurrency(service.base_price) }}</p>
                    </div>
                </div>

                <div v-if="service.estimated_time" class="space-y-1">
                    <p class="text-sm font-medium text-gray-500">Tempo Estimado</p>
                    <p class="text-sm">{{ service.estimated_time }} minutos</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-gray-500">Criado em</p>
                        <p class="text-sm">{{ formatDate(service.created_at) }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-medium text-gray-500">Atualizado em</p>
                        <p class="text-sm">{{ formatDate(service.updated_at) }}</p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
