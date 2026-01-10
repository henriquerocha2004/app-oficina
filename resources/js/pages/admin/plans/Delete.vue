<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import { toast } from 'vue-sonner';
import type { SubscriptionPlan } from './types';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle } from 'lucide-vue-next';

interface Props {
    show: boolean;
    plan: SubscriptionPlan | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
    (e: 'deleted'): void;
}>();

const deletePlan = () => {
    if (!props.plan) return;

    router.delete(admin.plans.destroy.url({ id: props.plan.id }), {
        onSuccess: () => {
            toast.success('Plano excluído com sucesso!', { position: 'top-right' });
            emit('deleted');
            emit('update:show', false);
        },
        onError: () => {
            toast.error('Erro ao excluir plano', { position: 'top-right' });
        },
    });
};

const closeDialog = () => {
    emit('update:show', false);
};
</script>

<template>
    <Dialog :open="show" @update:open="closeDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <AlertTriangle class="h-5 w-5 text-destructive" />
                    Excluir Plano
                </DialogTitle>
                <DialogDescription>
                    Esta ação não pode ser desfeita. Tem certeza que deseja excluir o plano
                    <strong>{{ plan?.name }}</strong>?
                </DialogDescription>
            </DialogHeader>

            <DialogFooter>
                <Button type="button" variant="outline" @click="closeDialog">
                    Cancelar
                </Button>
                <Button type="button" variant="destructive" @click="deletePlan">
                    Excluir
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
