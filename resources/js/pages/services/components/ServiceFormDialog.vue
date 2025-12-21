<script setup lang="ts">
import { ref, watch } from 'vue';
import { Service, ServiceFormData } from '@/types';
import axios from 'axios';
import {
    Dialog,
    DialogContent,
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

const props = withDefaults(
    defineProps<{
        open: boolean;
        service?: Service;
        title: string;
    }>(),
    {
        service: undefined,
    }
);

const emit = defineEmits<{
    'update:open': [value: boolean];
    success: [];
}>();

const formData = ref<ServiceFormData>({
    name: '',
    description: '',
    base_price: '',
    category: 'maintenance',
    estimated_time: '',
    is_active: true,
});

const loading = ref(false);
const errors = ref<Record<string, string[]>>({});

watch(
    () => props.service,
    (service) => {
        if (service) {
            formData.value = {
                name: service.name,
                description: service.description || '',
                base_price: service.base_price.toString(),
                category: service.category,
                estimated_time: service.estimated_time?.toString() || '',
                is_active: service.is_active,
            };
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

function resetForm() {
    formData.value = {
        name: '',
        description: '',
        base_price: '',
        category: 'maintenance',
        estimated_time: '',
        is_active: true,
    };
    errors.value = {};
}

async function handleSubmit() {
    loading.value = true;
    errors.value = {};

    try {
        const url = props.service ? `/services/${props.service.id}` : '/services';
        const method = props.service ? 'put' : 'post';

        await axios[method](url, formData.value);

        emit('success');
        resetForm();
    } catch (error: any) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Nome do Serviço *</Label>
                    <Input id="name" v-model="formData.name" placeholder="Ex: Troca de Óleo" />
                    <span v-if="errors.name" class="text-sm text-red-500">{{ errors.name[0] }}</span>
                </div>

                <div class="space-y-2">
                    <Label for="description">Descrição</Label>
                    <Textarea id="description" v-model="formData.description"
                        placeholder="Descrição detalhada do serviço" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="base_price">Preço Base (R$) *</Label>
                        <Input id="base_price" type="number" step="0.01" v-model="formData.base_price"
                            placeholder="150.00" />
                        <span v-if="errors.base_price" class="text-sm text-red-500">{{ errors.base_price[0] }}</span>
                    </div>

                    <div class="space-y-2">
                        <Label for="estimated_time">Tempo Estimado (min)</Label>
                        <Input id="estimated_time" type="number" v-model="formData.estimated_time" placeholder="60" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="category">Categoria *</Label>
                    <Select v-model="formData.category">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione a categoria" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="maintenance">Manutenção</SelectItem>
                            <SelectItem value="repair">Reparo</SelectItem>
                            <SelectItem value="diagnostic">Diagnóstico</SelectItem>
                            <SelectItem value="painting">Pintura</SelectItem>
                            <SelectItem value="alignment">Alinhamento</SelectItem>
                            <SelectItem value="other">Outro</SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="errors.category" class="text-sm text-red-500">{{ errors.category[0] }}</span>
                </div>

                <div class="flex items-center space-x-2">
                    <Checkbox id="is_active" v-model:checked="formData.is_active" />
                    <Label for="is_active">Serviço Ativo</Label>
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <Button type="button" variant="outline" @click="emit('update:open', false)">
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="loading">
                        {{ loading ? 'Salvando...' : 'Salvar' }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
