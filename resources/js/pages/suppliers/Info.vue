<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { cnpj } from 'cpf-cnpj-validator';
import type { SupplierInterface } from './types';

interface Props {
    show: boolean;
    supplier: SupplierInterface;
}

defineProps<Props>();
const emit = defineEmits<{
    close: [];
}>();

const formatCNPJ = (value: string) => {
    return cnpj.format(value);
};
</script>

<template>
    <Dialog :open="show" @update:open="(val) => !val && emit('close')">
        <DialogContent class="max-w-2xl">
            <DialogHeader>
                <DialogTitle>Informações do Fornecedor</DialogTitle>
            </DialogHeader>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ supplier.name }}</h3>
                    <div class="flex gap-2 mt-2">
                        <Badge :variant="supplier.is_active ? 'default' : 'secondary'">
                            {{ supplier.is_active ? 'Ativo' : 'Inativo' }}
                        </Badge>
                    </div>
                </div>

                <Separator />

                <div class="grid grid-cols-2 gap-4">
                    <div v-if="supplier.cnpj">
                        <p class="text-sm font-medium text-muted-foreground">CNPJ</p>
                        <p class="text-sm">{{ formatCNPJ(supplier.cnpj) }}</p>
                    </div>

                    <div v-if="supplier.contact_person">
                        <p class="text-sm font-medium text-muted-foreground">
                            Pessoa de Contato
                        </p>
                        <p class="text-sm">{{ supplier.contact_person }}</p>
                    </div>

                    <div v-if="supplier.email">
                        <p class="text-sm font-medium text-muted-foreground">Email</p>
                        <p class="text-sm">{{ supplier.email }}</p>
                    </div>

                    <div v-if="supplier.phone">
                        <p class="text-sm font-medium text-muted-foreground">Telefone</p>
                        <p class="text-sm">{{ supplier.phone }}</p>
                    </div>

                    <div v-if="supplier.website" class="col-span-2">
                        <p class="text-sm font-medium text-muted-foreground">Website</p>
                        <a
                            :href="supplier.website"
                            target="_blank"
                            class="text-sm text-primary hover:underline"
                        >
                            {{ supplier.website }}
                        </a>
                    </div>
                </div>

                <div v-if="supplier.address || supplier.city">
                    <Separator class="my-4" />
                    <h4 class="text-sm font-semibold mb-2">Endereço</h4>
                    <div class="space-y-2">
                        <p v-if="supplier.address" class="text-sm">{{ supplier.address }}</p>
                        <p v-if="supplier.city || supplier.state" class="text-sm">
                            {{ supplier.city }}{{ supplier.city && supplier.state ? ' - ' : '' }}{{ supplier.state }}
                        </p>
                        <p v-if="supplier.zip_code" class="text-sm">
                            CEP: {{ supplier.zip_code }}
                        </p>
                    </div>
                </div>

                <div v-if="supplier.notes">
                    <Separator class="my-4" />
                    <h4 class="text-sm font-semibold mb-2">Observações</h4>
                    <p class="text-sm text-muted-foreground">{{ supplier.notes }}</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
