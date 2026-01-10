<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { cnpj } from 'cpf-cnpj-validator';
import { useViaCep } from '@/composables/useViaCep';
import brasilianStates from '@/constants/brasilianStates';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { SupplierInterface } from './types';

interface Props {
    supplier?: SupplierInterface | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    submitted: [data: { mode: 'create' | 'edit'; data: any }];
}>();

const mode = ref<'create' | 'edit'>('create');
const { fetchAddress } = useViaCep();
const isProgramaticZipcodeChange = ref(false);
let debounceTimeout: ReturnType<typeof setTimeout> | null = null;

const formSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Nome é obrigatório').max(255),
        document_number: z.string()
            .min(1, 'CNPJ é obrigatório')
            .refine(
                (val) => !val || cnpj.isValid(val),
                'CNPJ inválido'
            ),
        email: z.string().email('Email inválido').optional().or(z.literal('')),
        phone: z.string().max(20).optional(),
        website: z.string().url('URL inválida').optional().or(z.literal('')),
        contact_person: z.string().max(100).optional(),
        address: z.string().optional(),
        city: z.string().max(100).optional(),
        state: z.string().max(2).optional(),
        zip_code: z.string().max(10).optional(),
        notes: z.string().optional(),
        is_active: z.boolean().default(true),
    })
);

const { handleSubmit, defineField, values, setValues, resetForm } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: '',
        document_number: '',
        email: '',
        phone: '',
        website: '',
        contact_person: '',
        address: '',
        city: '',
        state: '',
        zip_code: '',
        notes: '',
        is_active: true,
    },
});

const [name, nameAttrs] = defineField('name');
const [document_number, documentNumberAttrs] = defineField('document_number');
const [email, emailAttrs] = defineField('email');
const [phone, phoneAttrs] = defineField('phone');
const [website, websiteAttrs] = defineField('website');
const [contact_person, contactPersonAttrs] = defineField('contact_person');
const [address, addressAttrs] = defineField('address');
const [city, cityAttrs] = defineField('city');
const [state, stateAttrs] = defineField('state');
const [zip_code, zipCodeAttrs] = defineField('zip_code');
const [notes, notesAttrs] = defineField('notes');
const [is_active] = defineField('is_active');

const isActiveModel = computed({
    get: () => is_active.value,
    set: (val) => { is_active.value = val; }
});

async function fetchAddressByZipcode(zipcode: string | undefined) {
    if (!zipcode || isProgramaticZipcodeChange.value) return;
    
    const data = await fetchAddress(zipcode);
    if (data) {
        isProgramaticZipcodeChange.value = true;
        setValues({
            address: data.logradouro || '',
            city: data.localidade || '',
            state: data.uf || '',
        }, true);
        
        nextTick(() => {
            isProgramaticZipcodeChange.value = false;
        });
    }
}

onMounted(() => {
    if (props.supplier) {
        mode.value = 'edit';
        fillValues();
    }
});

function fillValues() {
    if (props.supplier) {
        isProgramaticZipcodeChange.value = true;
        setValues({
            name: props.supplier.name,
            document_number: props.supplier.document_number || '',
            email: props.supplier.email || '',
            phone: props.supplier.phone || '',
            website: props.supplier.website || '',
            contact_person: props.supplier.contact_person || '',
            address: props.supplier.address || '',
            city: props.supplier.city || '',
            state: props.supplier.state || '',
            zip_code: props.supplier.zip_code || '',
            notes: props.supplier.notes || '',
            is_active: props.supplier.is_active,
        });
        
        nextTick(() => {
            isProgramaticZipcodeChange.value = false;
        });
    }
}

const onSubmit = handleSubmit((values) => {
    emit('submitted', { mode: mode.value, data: values });
});

function clear() {
    resetForm();
    isProgramaticZipcodeChange.value = true;
    mode.value = 'create';
    
    nextTick(() => {
        isProgramaticZipcodeChange.value = false;
    });
}

defineExpose({ clear });

const isEditing = computed(() => !!props.supplier);

// CNPJ Mask
const formatCNPJ = (value: string) => {
    const cleaned = value.replace(/\D/g, '');
    return cnpj.format(cleaned);
};

watch(document_number, (newValue) => {
    if (newValue && newValue.replace(/\D/g, '').length === 14) {
        document_number.value = formatCNPJ(newValue);
    }
});

watch(zip_code, (newZipcode) => {
    if (isProgramaticZipcodeChange.value) return;
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchAddressByZipcode(newZipcode);
    }, 500);
});
</script>

<template>
    <form @submit="onSubmit" class="space-y-6">
        <Tabs default-value="cadastral" class="w-full">
            <TabsList class="grid w-full grid-cols-2">
                <TabsTrigger value="cadastral">Dados Cadastrais</TabsTrigger>
                <TabsTrigger value="contact">Contato e Endereço</TabsTrigger>
            </TabsList>

            <TabsContent value="cadastral" class="space-y-4 pt-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <Label for="name">Nome *</Label>
                        <Input
                            id="name"
                            v-model="name"
                            v-bind="nameAttrs"
                            placeholder="Nome do fornecedor"
                        />
                    </div>

                    <div class="space-y-3">
                        <Label for="document_number">CNPJ *</Label>
                        <Input
                            id="document_number"
                            v-model="document_number"
                            v-bind="documentNumberAttrs"
                            placeholder="00.000.000/0000-00"
                            maxlength="18"
                        />
                    </div>
                </div>

                <div class="space-y-3">
                    <Label for="contact_person">Pessoa de Contato</Label>
                    <Input
                        id="contact_person"
                        v-model="contact_person"
                        v-bind="contactPersonAttrs"
                        placeholder="Nome da pessoa responsável"
                        maxlength="100"
                    />
                </div>

                <div class="space-y-3">
                    <Label for="notes">Observações</Label>
                    <Textarea
                        id="notes"
                        v-model="notes"
                        v-bind="notesAttrs"
                        placeholder="Informações adicionais sobre o fornecedor"
                        rows="4"
                    />
                </div>

                <div class="flex items-center space-x-2">
                    <Switch
                        id="is_active"
                        v-model="isActiveModel"
                    />
                    <Label for="is_active" class="cursor-pointer">Fornecedor ativo</Label>
                </div>
            </TabsContent>

            <TabsContent value="contact" class="space-y-4 pt-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="email"
                            v-bind="emailAttrs"
                            type="email"
                            placeholder="email@fornecedor.com.br"
                        />
                    </div>

                    <div class="space-y-3">
                        <Label for="phone">Telefone</Label>
                        <Input
                            id="phone"
                            v-model="phone"
                            v-bind="phoneAttrs"
                            placeholder="(00) 00000-0000"
                            maxlength="20"
                        />
                    </div>
                </div>

                <div class="space-y-3">
                    <Label for="website">Website</Label>
                    <Input
                        id="website"
                        v-model="website"
                        v-bind="websiteAttrs"
                        placeholder="https://www.fornecedor.com.br"
                    />
                </div>

                <div class="space-y-3">
                    <Label for="zip_code">CEP</Label>
                    <Input
                        id="zip_code"
                        v-model="zip_code"
                        v-bind="zipCodeAttrs"
                        placeholder="00000-000"
                        maxlength="10"
                    />
                </div>

                <div class="space-y-3">
                    <Label for="address">Endereço</Label>
                    <Input
                        id="address"
                        v-model="address"
                        v-bind="addressAttrs"
                        placeholder="Rua, número, complemento"
                    />
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-3 col-span-2">
                        <Label for="city">Cidade</Label>
                        <Input
                            id="city"
                            v-model="city"
                            v-bind="cityAttrs"
                            placeholder="Nome da cidade"
                            maxlength="100"
                        />
                    </div>

                    <div class="space-y-3">
                        <Label for="state">Estado</Label>
                        <Select v-model="state" v-bind="stateAttrs">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Selecione o estado" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="s in brasilianStates"
                                    :key="s.value"
                                    :value="s.value"
                                >
                                    {{ s.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </TabsContent>
        </Tabs>

        <div class="flex justify-end gap-2">
            <Button type="submit">
                {{ isEditing ? 'Atualizar' : 'Criar' }} Fornecedor
            </Button>
        </div>
    </form>
</template>
