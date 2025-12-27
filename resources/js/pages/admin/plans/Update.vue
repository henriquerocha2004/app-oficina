<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import { toast } from 'vue-sonner';
import { ref, watch } from 'vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
    planData: SubscriptionPlan | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
    (e: 'updated'): void;
}>();

const form = useForm({
    name: '',
    slug: '',
    description: '',
    price: 0,
    billing_cycle: 'monthly' as 'monthly' | 'yearly',
    max_users: 1,
    max_clients: 0,
    max_vehicles: 0,
    max_services_per_month: 0,
    features: '',
    is_active: true,
});

// Ref local para o checkbox
const isActiveCheckbox = ref(true);

function changeCheckbox(value: boolean | "indeterminate") {
    form.is_active = value === true;
    isActiveCheckbox.value = form.is_active;
}

watch(
    () => props.planData,
    (plan) => {
        if (plan) {
            form.name = plan.name;
            form.slug = plan.slug;
            form.description = plan.description || '';
            form.price = plan.price;
            form.billing_cycle = plan.billing_cycle;
            form.max_users = plan.max_users;
            form.max_clients = plan.max_clients;
            form.max_vehicles = plan.max_vehicles;
            form.max_services_per_month = plan.max_services_per_month;
            form.features = Array.isArray(plan.features) ? plan.features.join(', ') : '';
            form.is_active = Boolean(plan.is_active);
            isActiveCheckbox.value = Boolean(plan.is_active);
        }
    },
    { immediate: true }
);

const submit = () => {
    if (!props.planData) return;

    form.put(admin.plans.update.url({ id: props.planData.id }), {
        onSuccess: () => {
            toast.success('Plano atualizado com sucesso!', { position: 'top-right' });
            emit('updated');
            emit('update:show', false);
        },
        onError: () => {
            toast.error('Erro ao atualizar plano', { position: 'top-right' });
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
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Plano de Assinatura</DialogTitle>
                <DialogDescription>
                    Atualize as informações do plano de assinatura
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name">Nome do Plano *</Label>
                        <Input id="name" v-model="form.name" placeholder="Ex: Plano Básico" required />
                        <span v-if="form.errors.name" class="text-sm text-destructive">
                            {{ form.errors.name }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <Label for="slug">Slug *</Label>
                        <Input id="slug" v-model="form.slug" placeholder="Ex: plano-basico" required />
                        <span v-if="form.errors.slug" class="text-sm text-destructive">
                            {{ form.errors.slug }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="description">Descrição</Label>
                    <Textarea id="description" v-model="form.description" placeholder="Descrição do plano" rows="3" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="price">Preço (R$) *</Label>
                        <Input id="price" v-model.number="form.price" type="number" step="0.01" min="0" required />
                        <span v-if="form.errors.price" class="text-sm text-destructive">
                            {{ form.errors.price }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <Label for="billing_cycle">Ciclo de Cobrança *</Label>
                        <Select v-model="form.billing_cycle">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o ciclo" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="monthly">Mensal</SelectItem>
                                <SelectItem value="yearly">Anual</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="max_users">Máx. Usuários *</Label>
                        <Input id="max_users" v-model.number="form.max_users" type="number" min="1" required />
                    </div>

                    <div class="space-y-2">
                        <Label for="max_clients">Máx. Clientes (0 = ilimitado)</Label>
                        <Input id="max_clients" v-model.number="form.max_clients" type="number" min="0" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="max_vehicles">Máx. Veículos (0 = ilimitado)</Label>
                        <Input id="max_vehicles" v-model.number="form.max_vehicles" type="number" min="0" />
                    </div>

                    <div class="space-y-2">
                        <Label for="max_services">Máx. Serviços/Mês (0 = ilimitado)</Label>
                        <Input id="max_services" v-model.number="form.max_services_per_month" type="number" min="0" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="features">Recursos (separados por vírgula)</Label>
                    <Textarea id="features" v-model="form.features"
                        placeholder="Ex: Gestão de clientes, Controle de estoque, Relatórios" rows="2" />
                </div>

                <div class="flex items-center space-x-2">
                    <Checkbox v-model="isActiveCheckbox" @update:model-value="changeCheckbox" />
                    <Label for="is_active" class="cursor-pointer">Plano ativo</Label>
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
