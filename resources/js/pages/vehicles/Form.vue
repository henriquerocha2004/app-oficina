<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { FormField } from '@/components/ui/form';
import FormControl from '@/components/ui/form/FormControl.vue';
import FormItem from '@/components/ui/form/FormItem.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormMessage from '@/components/ui/form/FormMessage.vue';
import Input from '@/components/ui/input/Input.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Separator } from '@/components/ui/separator';
import { toTypedSchema } from '@vee-validate/zod';
import { z } from 'zod';
import { useForm } from 'vee-validate';
import { ref, onMounted, nextTick } from 'vue';
import { VehiclesInterface, VehicleType, FuelType, TransmissionType } from './types';
import ClientCombobox from './components/ClientCombobox.vue';
import { useClientsSearch } from './composables/useClientsSearch';

const props = defineProps<{ vehicle?: VehiclesInterface | null }>();
const emit = defineEmits(['submitted']);
const mode = ref<'create' | 'edit'>('create');

const { clients, isLoading, searchClients, clearSearch, setClient, minSearchLength } = useClientsSearch();

const vehicleTypes: Array<{ value: VehicleType; label: string }> = [
    { value: 'car', label: 'Carro' },
    { value: 'motorcycle', label: 'Moto' },
];

const fuelTypes: Array<{ value: FuelType; label: string }> = [
    { value: 'alcohol', label: 'Álcool' },
    { value: 'gasoline', label: 'Gasolina' },
    { value: 'diesel', label: 'Diesel' },
];

const transmissionTypes: Array<{ value: TransmissionType; label: string }> = [
    { value: 'manual', label: 'Manual' },
    { value: 'automatic', label: 'Automática' },
];

const currentYear = new Date().getFullYear();

const schema = toTypedSchema(z.object({
    client_id: z.string({ message: 'Selecione o cliente' }).min(1, { message: 'Cliente é obrigatório' }),
    brand: z.string({ message: 'Informe a marca' }).min(2, { message: 'Marca deve ter pelo menos 2 caracteres' }),
    model: z.string({ message: 'Informe o modelo' }).min(2, { message: 'Modelo deve ter pelo menos 2 caracteres' }),
    year: z.number({ message: 'Informe o ano' })
        .min(1900, { message: 'Ano deve ser maior que 1900' })
        .max(currentYear + 1, { message: `Ano deve ser menor ou igual a ${currentYear + 1}` }),
    plate: z.string({ message: 'Informe a placa' })
        .min(7, { message: 'Placa deve ter 7 caracteres' })
        .max(8, { message: 'Placa deve ter no máximo 8 caracteres' })
        .regex(/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/, { message: 'Formato de placa inválido (ABC1234 ou ABC1D23)' }),
    color: z.string().optional().or(z.literal('')),
    type: z.enum(['car', 'motorcycle'], { message: 'Selecione o tipo do veículo' }).default('car'),

    displacement: z.string().optional().or(z.literal('')),
    fuel: z.string().optional().or(z.literal('')),
    transmission: z.string().optional().or(z.literal('')),
    mileage: z.number().positive('Quilometragem deve ser positiva').optional().or(z.literal('')),
    chassis: z.string().optional().or(z.literal('')),

    observations: z.string().optional().or(z.literal('')),
}));

const form = useForm({
    validationSchema: schema,
    initialValues: {
        type: 'car',
    },
});

const onSubmit = form.handleSubmit((values) => {
    emit('submitted', { mode: mode.value, data: normalizeFormData(values) });
});

onMounted(async () => {
    if (props.vehicle) {
        mode.value = 'edit';
        fillValues();
    }
});

function normalizeFormData(data: any): VehiclesInterface {
    return {
        client_id: data.client_id,
        brand: data.brand,
        model: data.model,
        year: data.year,
        plate: data.plate?.toUpperCase() || '',
        color: data.color || '',
        vehicle_type: data.type,
        cilinder_capacity: data.displacement || '',
        fuel: data.fuel || undefined,
        transmission: data.transmission || undefined,
        mileage: data.mileage ? Number(data.mileage) : undefined,
        vin: data.chassis || '',
        observations: data.observations || '',
    };
}

async function fillValues() {
    if (!props.vehicle) return;

    if (props.vehicle.client_id) {
        const clientOption = {
            id: props.vehicle.client_id,
            name: props.vehicle.client?.name || 'Cliente não informado',
            document_number: props.vehicle.client?.document_number || '',
        };
        setClient(clientOption);
    }

    form.setValues({
        client_id: props.vehicle.client_id || '',
        brand: props.vehicle.brand || '',
        model: props.vehicle.model || '',
        year: props.vehicle.year || new Date().getFullYear(),
        plate: props.vehicle.plate || '',
        color: props.vehicle.color || '',
        type: props.vehicle.vehicle_type || 'car',
        displacement: props.vehicle.cilinder_capacity || '',
        fuel: props.vehicle.fuel || '',
        transmission: props.vehicle.transmission || '',
        mileage: props.vehicle.mileage || '',
        chassis: props.vehicle.vin || '',
        observations: props.vehicle.observations || '',
    });
}

function clear() {
    form.resetForm();
    clearSearch();
    mode.value = 'create';
}

function formatLicensePlate(value: string) {
    if (!value) return '';

    const cleaned = value.replace(/[^A-Z0-9]/gi, '').toUpperCase();

    if (cleaned.length <= 3) {
        return cleaned;
    } else if (cleaned.length <= 7) {
        return cleaned.slice(0, 7);
    } else {
        return cleaned.slice(0, 7);
    }
}

defineExpose({
    clear,
});
</script>

<template>
    <div>
        <form @submit.prevent="onSubmit">
            <div class="flex flex-col gap-4">
                <!-- Informações Básicas -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium">Informações Básicas</h3>

                    <FormField v-slot="{ componentField }" name="client_id">
                        <FormItem>
                            <FormLabel>Cliente *</FormLabel>
                            <FormControl>
                                <ClientCombobox v-model="componentField.modelValue" :clients="clients"
                                    :is-loading="isLoading" :disabled="isLoading" :min-search-length="minSearchLength"
                                    placeholder="Selecione um cliente..."
                                    search-placeholder="Digite pelo menos 3 caracteres para buscar..."
                                    empty-text="Nenhum cliente encontrado." @search="searchClients"
                                    @update:modelValue="componentField.onChange" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <FormField v-slot="{ componentField }" name="brand">
                            <FormItem>
                                <FormLabel>Marca *</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Ex: Toyota" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="model">
                            <FormItem>
                                <FormLabel>Modelo *</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Ex: Corolla" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <FormField v-slot="{ componentField }" name="year">
                            <FormItem>
                                <FormLabel>Ano *</FormLabel>
                                <FormControl>
                                    <Input type="number" :min="1900" :max="currentYear + 1" placeholder="Ex: 2020"
                                        v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="plate">
                            <FormItem>
                                <FormLabel>Placa *</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="ABC1234" maxlength="8"
                                        :model-value="componentField.modelValue"
                                        @update:model-value="(value: string | number) => componentField.onChange(formatLicensePlate(String(value)))" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="color">
                            <FormItem>
                                <FormLabel>Cor</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Ex: Branco" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <FormField v-slot="{ componentField }" name="type">
                        <FormItem>
                            <FormLabel>Tipo *</FormLabel>
                            <Select v-bind="componentField">
                                <FormControl>
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione o tipo" />
                                    </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="type in vehicleTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <Separator />

                <!-- Informações Técnicas -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium">Informações Técnicas</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <FormField v-slot="{ componentField }" name="displacement">
                            <FormItem>
                                <FormLabel>Cilindradas</FormLabel>
                                <FormControl>
                                    <Input type="text" placeholder="Ex: 1.6, 2.0" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="fuel">
                            <FormItem>
                                <FormLabel>Combustível</FormLabel>
                                <Select v-bind="componentField">
                                    <FormControl>
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Selecione o combustível" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="fuel in fuelTypes" :key="fuel.value" :value="fuel.value">
                                                {{ fuel.label }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <FormField v-slot="{ componentField }" name="transmission">
                            <FormItem>
                                <FormLabel>Transmissão</FormLabel>
                                <Select v-bind="componentField">
                                    <FormControl>
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Selecione a transmissão" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="transmission in transmissionTypes"
                                                :key="transmission.value" :value="transmission.value">
                                                {{ transmission.label }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="mileage">
                            <FormItem>
                                <FormLabel>Quilometragem</FormLabel>
                                <FormControl>
                                    <Input type="number" min="0" placeholder="Ex: 50000" v-bind="componentField" />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <FormField v-slot="{ componentField }" name="chassis">
                        <FormItem>
                            <FormLabel>Chassi</FormLabel>
                            <FormControl>
                                <Input type="text" placeholder="Número do chassi" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <Separator />

                <!-- Observações -->
                <FormField v-slot="{ componentField }" name="observations">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea placeholder="Observações sobre o veículo" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <Button type="submit" class="w-full">
                    {{ mode === 'create' ? 'Cadastrar Veículo' : 'Atualizar Veículo' }}
                </Button>
            </div>
        </form>
    </div>
</template>