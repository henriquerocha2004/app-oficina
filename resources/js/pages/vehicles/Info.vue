<script setup lang="ts">
import {
    Drawer,
    DrawerContent,
    DrawerHeader,
    DrawerTitle,
} from '@/components/ui/drawer';

import { Wrench } from 'lucide-vue-next';
import { VehiclesInterface } from './types';
import formatPhone from '@/utils/formatPhone';

export interface VehicleInfoProps {
    show: boolean;
    vehicle?: VehiclesInterface | null;
}

const props = defineProps<VehicleInfoProps>();

// Dados hardcoded para histórico de manutenção
const maintenanceHistory = [
    {
        id: '1',
        service: 'Troca de óleo e filtros',
        date: '14/01/2024',
        mileage: '45.000 km',
        price: 'R$ 180,00',
        status: 'Concluído',
    },
    {
        id: '2',
        service: 'Revisão dos 40.000 km',
        date: '19/11/2023',
        mileage: '40.000 km',
        price: 'R$ 450,00',
        status: 'Concluído',
    },
    {
        id: '3',
        service: 'Troca de pastilhas de freio',
        date: '09/08/2023',
        mileage: '38.000 km',
        price: 'R$ 320,00',
        status: 'Concluído',
    },
];

function getVehicleTypeLabel(type?: string): string {
    if (type === 'car') return 'Carro';
    if (type === 'motorcycle') return 'Moto';
    return '-';
}

function getFuelTypeLabel(fuel?: string): string {
    if (fuel === 'alcohol') return 'Álcool';
    if (fuel === 'gasoline') return 'Gasolina';
    if (fuel === 'diesel') return 'Diesel';
    return 'Flex';
}

function getTransmissionLabel(transmission?: string): string {
    if (transmission === 'manual') return 'Manual';
    if (transmission === 'automatic') return 'Automática';
    return 'Automatic';
}

</script>
<template>
    <div>
        <Drawer :open="props.show" @update:open="(value) => $emit('update:show', value)">
            <DrawerContent class="max-h-[90vh]">
                <DrawerHeader>
                    <DrawerTitle class="text-xl">{{ props.vehicle?.brand }} {{ props.vehicle?.model }} ({{
                        props.vehicle?.year }})</DrawerTitle>
                    <p class="text-sm text-gray-500">Placa: {{ props.vehicle?.plate }}</p>
                </DrawerHeader>
                <div class="flex flex-row gap-4 p-4 overflow-y-auto">
                    <!-- Informações do Veículo -->
                    <div class="flex flex-col gap-4 border-r-2 w-1/3 pr-4">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">
                            Informações do Veículo
                        </h2>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Marca</p>
                            <p class="font-bold">{{ props.vehicle?.brand }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Modelo</p>
                            <p class="font-bold">{{ props.vehicle?.model }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Ano</p>
                            <p class="font-bold">{{ props.vehicle?.year }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Placa</p>
                            <p class="font-bold">{{ props.vehicle?.plate }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Cor</p>
                            <p class="font-bold">{{ props.vehicle?.color || '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Tipo</p>
                            <p class="font-bold">{{ getVehicleTypeLabel(props.vehicle?.vehicle_type) }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Combustível</p>
                            <p class="font-bold">{{ getFuelTypeLabel(props.vehicle?.fuel) }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Transmissão</p>
                            <p class="font-bold">{{ getTransmissionLabel(props.vehicle?.transmission) }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Quilometragem</p>
                            <p class="font-bold">{{ props.vehicle?.mileage ?
                                `${props.vehicle.mileage.toLocaleString('pt-BR')} km` : '45.000 km' }}</p>
                        </div>
                    </div>

                    <!-- Informações do Proprietário -->
                    <div class="flex flex-col gap-4 border-r-2 w-1/3 pr-4">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">
                            Informações do Proprietário
                        </h2>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Nome</p>
                            <p class="font-bold">{{ props.vehicle?.client?.name || '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Telefone</p>
                            <p class="font-bold">{{ props.vehicle?.client?.phone ?
                                formatPhone(props.vehicle.client.phone) : '-' }}</p>
                        </div>
                    </div>

                    <!-- Histórico de Manutenção -->
                    <div class="flex flex-col gap-4 w-1/3">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">
                            Histórico de Manutenção
                        </h2>
                        <div class="flex flex-col gap-3 max-h-[500px] overflow-y-auto">
                            <div v-for="maintenance in maintenanceHistory" :key="maintenance.id"
                                class="flex flex-col gap-2 rounded-lg bg-gray-100 dark:bg-gray-700 p-4">
                                <div class="flex flex-row gap-2 items-center">
                                    <Wrench class="h-5 w-5" />
                                    <p class="font-semibold">{{ maintenance.service }}</p>
                                </div>
                                <div class="flex flex-row justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">{{ maintenance.date }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ maintenance.mileage }}</span>
                                </div>
                                <div class="flex flex-row justify-between items-center">
                                    <span class="font-bold text-lg">{{ maintenance.price }}</span>
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded text-xs font-semibold">
                                        {{ maintenance.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="flex flex-col gap-2 mt-4 pt-4 border-t-2">
                            <h3 class="text-base font-semibold border-b-1 border-black dark:border-white mb-2">
                                Observações
                            </h3>
                            <p class="text-sm">
                                {{ props.vehicle?.observations || 'Veículo em excelente estado de conservação. Cliente
                                muito cuidadoso.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </DrawerContent>
        </Drawer>
    </div>
</template>
