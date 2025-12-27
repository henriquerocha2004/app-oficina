<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import { toast } from 'vue-sonner';
import { ref, watch } from 'vue';
import type { Tenant } from './types';
import type { SubscriptionPlan } from '../plans/types';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';

interface Props {
    show: boolean;
    tenantData: Tenant | null;
    subscriptionPlans: SubscriptionPlan[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
    (e: 'updated'): void;
}>();

const form = useForm({
    name: '',
    subscription_plan_id: '',
    is_active: true,
});

const isActiveCheckbox = ref<boolean>(props.tenantData?.is_active || true);

function changeCheckbox(value: boolean | "indeterminate") {
    form.is_active = value === true;
    isActiveCheckbox.value = form.is_active;
}

watch(
    () => props.tenantData,
    (tenant) => {
        if (tenant) {
            form.name = tenant.name;
            form.subscription_plan_id = tenant.subscription_plan_id;
            form.is_active = Boolean(tenant.is_active);
            isActiveCheckbox.value = Boolean(tenant.is_active);
        }
    },
    { immediate: true }
);

const submit = () => {
    if (!props.tenantData) return;
    form.put(admin.tenants.update.url({ id: props.tenantData.id }), {
        onSuccess: () => {
            toast.success('Oficina atualizada com sucesso!', { position: 'top-right' });
            emit('updated');
            emit('update:show', false);
        },
        onError: () => {
            toast.error('Erro ao atualizar oficina', { position: 'top-right' });
        },
    });
};

const closeDialog = () => {
    emit('update:show', false);
    form.reset();
};
</script>

<template>
    <Dialog :open="show" @update:open="closeDialog">
        <DialogContent class="max-w-lg">
            <DialogHeader>
                <DialogTitle>Editar Oficina</DialogTitle>
                <DialogDescription>
                    Atualize as informações da oficina
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Nome da Oficina asds *</Label>
                    <Input id="name" v-model="form.name" placeholder="Nome da oficina" required />
                    <span v-if="form.errors.name" class="text-sm text-destructive">
                        {{ form.errors.name }}
                    </span>
                </div>

                <div class="space-y-2">
                    <Label for="subscription_plan_id">Plano de Assinatura *</Label>
                    <Select v-model="form.subscription_plan_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione o plano" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="plan in subscriptionPlans" :key="plan.id" :value="plan.id.toString()">
                                {{ plan.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="form.errors.subscription_plan_id" class="text-sm text-destructive">
                        {{ form.errors.subscription_plan_id }}
                    </span>
                </div>

                <div class="flex items-center space-x-2">
                    <Checkbox id="is_active" v-model="isActiveCheckbox" @update:model-value="changeCheckbox" />
                    <Label for="is_active" class="cursor-pointer">Oficina ativa</Label>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="closeDialog">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Salvando...' : 'Salvar' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
