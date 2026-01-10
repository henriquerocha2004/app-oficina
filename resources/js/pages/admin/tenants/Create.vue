<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import { toast } from 'vue-sonner';
import type { SubscriptionPlan } from '../plans/types';
import { computed, ref, watch } from 'vue';
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
    subscriptionPlans: SubscriptionPlan[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
    (e: 'created'): void;
}>();

const form = useForm({
    name: '',
    slug: '',
    subscription_plan_id: '',
    domain: '',
    admin_name: '',
    admin_email: '',
    admin_password: '',
    is_active: true,
});

// Ref local para o checkbox
const isActiveCheckbox = ref(true);

// Sincronizar checkbox com form
function changeCheckbox(value: boolean | "indeterminate") {
    const boolValue = value === true;
    isActiveCheckbox.value = boolValue;
    form.is_active = boolValue;
}

// Auto-gerar slug baseado no nome
watch(
    () => form.name,
    (value) => {
        if (!form.slug || form.slug === '') {
            form.slug = value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
        }
    }
);

const activePlans = computed(() => {
    return props.subscriptionPlans.filter((plan) => plan.is_active);
});

const submit = () => {
    form.post(admin.tenants.index.url(), {
        onSuccess: () => {
            toast.success('Oficina criada com sucesso!', { position: 'top-right' });
            emit('created');
            emit('update:show', false);
            form.reset();
        },
        onError: () => {
            toast.error('Erro ao criar oficina', { position: 'top-right' });
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
                <DialogTitle>Nova Oficina</DialogTitle>
                <DialogDescription>
                    Preencha as informações para criar uma nova oficina
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name">Nome da Oficina *</Label>
                        <Input id="name" v-model="form.name" placeholder="Ex: Oficina Central" required />
                        <span v-if="form.errors.name" class="text-sm text-destructive">
                            {{ form.errors.name }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <Label for="slug">Slug *</Label>
                        <Input id="slug" v-model="form.slug" placeholder="Ex: oficina-central" required />
                        <span v-if="form.errors.slug" class="text-sm text-destructive">
                            {{ form.errors.slug }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="subscription_plan_id">Plano de Assinatura *</Label>
                        <Select v-model="form.subscription_plan_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o plano" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="plan in activePlans" :key="plan.id" :value="plan.id.toString()">
                                    {{ plan.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.subscription_plan_id" class="text-sm text-destructive">
                            {{ form.errors.subscription_plan_id }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <Label for="domain">Domínio *</Label>
                        <Input id="domain" v-model="form.domain" placeholder="Ex: oficina-central" required />
                        <span v-if="form.errors.domain" class="text-sm text-destructive">
                            {{ form.errors.domain }}
                        </span>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium mb-3">Dados do Administrador</h3>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="admin_name">Nome do Administrador *</Label>
                            <Input id="admin_name" v-model="form.admin_name" placeholder="Nome completo" required />
                            <span v-if="form.errors.admin_name" class="text-sm text-destructive">
                                {{ form.errors.admin_name }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="admin_email">E-mail *</Label>
                                <Input id="admin_email" v-model="form.admin_email" type="email"
                                    placeholder="admin@email.com" required />
                                <span v-if="form.errors.admin_email" class="text-sm text-destructive">
                                    {{ form.errors.admin_email }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="admin_password">Senha *</Label>
                                <Input id="admin_password" v-model="form.admin_password" type="password"
                                    placeholder="Mínimo 8 caracteres" required />
                                <span v-if="form.errors.admin_password" class="text-sm text-destructive">
                                    {{ form.errors.admin_password }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <Checkbox v-model="isActiveCheckbox" @update:model-value="changeCheckbox" />
                    <Label for="is_active" class="cursor-pointer">Oficina ativa</Label>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="closeDialog">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Criando...' : 'Criar Oficina' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
