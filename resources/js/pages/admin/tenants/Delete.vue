<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import { toast } from 'vue-sonner';
import type { Tenant } from './types';
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
    tenant: Tenant | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
    (e: 'deleted'): void;
}>();

const deleteTenant = () => {
    if (!props.tenant) return;

    router.delete(admin.tenants.destroy.url({ id: props.tenant.id }), {
        onSuccess: () => {
            toast.success('Oficina excluída com sucesso!', { position: 'top-right' });
            emit('deleted');
            emit('update:show', false);
        },
        onError: () => {
            toast.error('Erro ao excluir oficina', { position: 'top-right' });
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
                    Excluir Oficina
                </DialogTitle>
                <DialogDescription>
                    Esta ação não pode ser desfeita. Tem certeza que deseja excluir a oficina
                    <strong>{{ tenant?.name }}</strong> e todos os seus dados?
                </DialogDescription>
            </DialogHeader>

            <DialogFooter>
                <Button type="button" variant="outline" @click="closeDialog">
                    Cancelar
                </Button>
                <Button type="button" variant="destructive" @click="deleteTenant">
                    Excluir
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
