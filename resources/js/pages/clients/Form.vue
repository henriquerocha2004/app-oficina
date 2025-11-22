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
import brasilianStates from '@/constants/brasilianStates';
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod';
import { useForm } from 'vee-validate';
import { cnpj, cpf } from 'cpf-cnpj-validator';
import { watch, ref, onMounted, nextTick } from 'vue';
import { ClientInterface } from './types';

const props = defineProps<{ client?: ClientInterface | null }>();
const emit = defineEmits(['submitted']);
const mode = ref<'create' | 'edit'>('create');
const schema = toTypedSchema(z.object({
    name: z.string({ message: 'Informe o nome' }).min(3, { message: 'Nome deve ter pelo menos 3 caracteres' }),
    phone: z.string({ message: 'Informe o telefone' }).min(10, { message: 'Telefone deve ter pelo menos 10 caracteres' }),
    document: z.string({ message: 'Informe o CPF/CNPJ' })
        .min(11, { message: 'CPF/CNPJ deve ter pelo menos 11 caracteres' })
        .max(18, { message: 'CPF/CNPJ deve ter no máximo 18 caracteres' })
        .refine((val) => {
            const cleaned = val.replace(/\D/g, '');
            return cpf.isValid(cleaned) || cnpj.isValid(cleaned);
        }, { message: 'CPF/CNPJ inválido' }),
    email: z.string({ message: 'Informe o email' }).email({ message: 'Email inválido' }),
    address: z.string().optional().or(z.literal('')),
    city: z.string().optional().or(z.literal('')),
    state: z.string().optional().or(z.literal('')),
    zipcode: z.string().optional().or(z.literal('')),
    observations: z.string().optional().or(z.literal('')),
}))
const form = useForm({
    validationSchema: schema,
})
const onSubmit = form.handleSubmit((values) => {
    emit('submitted', { mode: mode.value, data: normalizeFormData(values) });
});
const isProgramaticZipcodeChange = ref(false);
let debounceTimeout: ReturnType<typeof setTimeout> | null = null;

onMounted(() => {
    if (props.client) {
        mode.value = 'edit';
        fillValues();
    }
})

function normalizeFormData(data: any): ClientInterface {
    return {
        name: data.name,
        phone: data.phone,
        document_number: data.document,
        email: data.email,
        street: data.address || '',
        city: data.city || '',
        state: data.state || '',
        zip_code: data.zipcode || '',
        observations: data.observations || '',
    };
}

function fillValues() {
    isProgramaticZipcodeChange.value = true;
    form.setValues({
        name: props.client?.name || '',
        phone: props.client?.phone || '',
        document: props.client?.document_number || '',
        email: props.client?.email || '',
        address: props.client?.street || '',
        city: props.client?.city || '',
        state: props.client?.state || '',
        zipcode: props.client?.zip_code || '',
        observations: props.client?.observations || '',
    });

    nextTick(() => {
        isProgramaticZipcodeChange.value = false;
    });
}

function fetchAddressByZipcode(zipcode: string | undefined) {
    if (!zipcode) return;
    const cleaned = zipcode.replace(/\D/g, '');
    if (cleaned.length !== 8) {
        return;
    }

    fetch(`https://viacep.com.br/ws/${cleaned}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                return;
            }

            form.setValues({
                address: data.logradouro || '',
                city: data.localidade || '',
                state: data.uf || '',
            }, true);
        })
        .catch(error => {
            console.error('Error fetching address:', error);
        });
}

function clear() {
    form.resetForm();
    isProgramaticZipcodeChange.value = true;
    mode.value = 'create';

    nextTick(() => {
        isProgramaticZipcodeChange.value = false;
    });
}

watch(() => form.values.zipcode, (newZipcode) => {
    if (isProgramaticZipcodeChange.value) return;
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchAddressByZipcode(newZipcode);
    }, 500);
});

defineExpose({
    clear,
});

</script>
<template>
    <div>
        <form @submit.prevent="onSubmit">
            <div class="flex flex-col gap-4">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome Completo</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Nome" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="phone">
                    <FormItem>
                        <FormLabel>Telefone</FormLabel>
                        <FormControl>
                            <Input type="tel" placeholder="Telefone" v-bind="componentField"
                                v-mask="['(##) ####-####', '(##) #####-####']" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="document">
                    <FormItem>
                        <FormLabel>CPF/CNPJ</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="CPF/CNPJ" v-bind="componentField"
                                v-mask="['###.###.###-##', '##.###.###/####-##']" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="email" placeholder="Email" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="zipcode">
                    <FormItem>
                        <FormLabel>CEP</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="CEP" v-bind="componentField" v-mask="['#####-###']" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="address">
                    <FormItem>
                        <FormLabel>Endereço</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Endereço" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="city">
                    <FormItem>
                        <FormLabel>Cidade</FormLabel>
                        <FormControl>
                            <Input type="text" placeholder="Cidade" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="state">
                    <FormItem>
                        <FormLabel>Estado</FormLabel>
                        <Select v-bind="componentField">
                            <FormControl>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecione o estado" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="state in brasilianStates" :key="state.value"
                                        :value="state.value">
                                        {{ state.label }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="observations">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea type="text" placeholder="Observações" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <Button type="submit"> Salvar </Button>
            </div>
        </form>
    </div>
</template>
