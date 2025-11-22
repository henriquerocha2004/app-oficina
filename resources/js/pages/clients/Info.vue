<script setup lang="ts">
import {
    Drawer,
    DrawerContent,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';

import { Car } from 'lucide-vue-next';
import { ClientInterface } from './types';
import { VehiclesInterface } from '@/pages/vehicles/types';
import formatPhone from '@/utils/formatPhone';
import formatDocument from '@/utils/formatDocument';
import { VehiclesApi } from '@/api/Vehicles';
import { ref, watch } from 'vue';

export interface UpdateClientProps {
    show: boolean;
    client?: ClientInterface | null;
}

const props = defineProps<UpdateClientProps>();
const vehicles = ref<VehiclesInterface[]>([]);
const isLoadingVehicles = ref(false);

async function loadVehicles() {
    if (!props.client?.id) return;

    isLoadingVehicles.value = true;
    try {
        const response = await VehiclesApi.getByClientId(props.client.id);
        vehicles.value = response.vehicles || [];
    } catch (error) {
        console.error('Error loading vehicles:', error);
        vehicles.value = [];
    } finally {
        isLoadingVehicles.value = false;
    }
}

watch(() => props.show, (newValue) => {
    if (newValue && props.client?.id) {
        loadVehicles();
    } else {
        vehicles.value = [];
    }
});

</script>
<template>
    <div>
        <Drawer :open="props.show" @update:open="(value) => $emit('update:show', value)">
            <DrawerContent>
                <DrawerHeader>
                    <DrawerTitle class="text-xl">Detalhes do Cliente</DrawerTitle>
                </DrawerHeader>
                <div class="flex flex-row gap-4 p-4">
                    <div class="flex flex-col gap-4 border-r-2 w-1/3 pr-4">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">Informações
                            Pessoais</h2>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Nome Completo</p>
                            <p class="font-bold">{{ props.client?.name }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Telefone</p>
                            <p class="font-bold">{{ formatPhone(props.client?.phone ?? '') }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">CPF/CNPJ</p>
                            <p class="font-bold">{{ formatDocument(props.client?.document_number ?? '') }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Email</p>
                            <p class="font-bold">{{ props.client?.email }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 border-r-2 w-1/3 pr-4">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">Endereço</h2>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Rua</p>
                            <p class="font-bold">{{ props.client?.street }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Cidade</p>
                            <p class="font-bold">{{ props.client?.city }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Estado</p>
                            <p class="font-bold">{{ props.client?.state }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">CEP</p>
                            <p class="font-bold">{{ props.client?.zip_code }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Observações</p>
                            <p class="font-bold">-</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 w-1/3">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">Veículos
                            Cadastrados</h2>
                        <div v-if="isLoadingVehicles" class="text-center text-gray-500">
                            Carregando veículos...
                        </div>
                        <div v-else-if="vehicles.length === 0" class="text-center text-gray-500">
                            Nenhum veículo cadastrado
                        </div>
                        <div v-else class="flex flex-col gap-3 max-h-96 overflow-y-auto">
                            <div v-for="vehicle in vehicles" :key="vehicle.id"
                                class="flex flex-col gap-1 rounded-3xl bg-gray-300 dark:bg-gray-700 p-5">
                                <div class="flex flex-row gap-2">
                                    <Car class="h-6 w-6" />
                                    <p class="font-semibold">{{ vehicle.brand }} {{ vehicle.model }}</p>
                                </div>
                                <div class="flex flex-row gap-1">
                                    <p class="font-bold">Placa: </p>
                                    <p class="font-bold">{{ vehicle.plate }}</p>
                                </div>
                                <div class="flex flex-row gap-1">
                                    <p class="font-bold">Ano: </p>
                                    <p class="font-bold">{{ vehicle.year }}</p>
                                </div>
                                <div v-if="vehicle.color" class="flex flex-row gap-1">
                                    <p class="font-bold">Cor: </p>
                                    <p class="font-bold">{{ vehicle.color }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div></div>
                </div>
            </DrawerContent>
        </Drawer>
    </div>
</template>