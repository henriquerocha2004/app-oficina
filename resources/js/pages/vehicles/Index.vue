<script setup lang="ts">
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { provide, ref, onBeforeUnmount } from 'vue';
import { VehiclesInterface } from './types';
import { columns } from './Table/columns';
import Table from './Table/Index.vue';
import Create from './Create.vue';
import Update from './Update.vue';
import Info from './Info.vue';
import { Card, CardContent } from '@/components/ui/card';
import { History, WrenchIcon, CircleAlertIcon, CheckCircle } from 'lucide-vue-next';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Veículos',
        href: "",
    },
];

const showCreate = ref<boolean>(false);
const showUpdate = ref<boolean>(false);
const showInfo = ref<boolean>(false);
const showDelete = ref<boolean>(false);
const vehicleToEdit = ref<VehiclesInterface | null>(null);
const vehicleToView = ref<VehiclesInterface | null>(null);
const vehicleDelete = ref<VehiclesInterface | null>(null);
const tableComponent = ref<InstanceType<typeof Table> | null>(null);

provide('onEditVehicle', (vehicle: VehiclesInterface) => {
    vehicleToEdit.value = vehicle;
    showUpdate.value = true;
});
provide('onViewVehicle', (vehicle: VehiclesInterface) => {
    vehicleToView.value = vehicle;
    showInfo.value = true;
});

provide('onDeleteVehicle', (vehicle: VehiclesInterface) => {
    vehicleDelete.value = vehicle;
    showDelete.value = true;
});

function refreshTable() {
    tableComponent.value?.fetchCars();
}

onBeforeUnmount(() => {
    showCreate.value = false;
    showUpdate.value = false;
    showInfo.value = false;
    showDelete.value = false;
    vehicleToEdit.value = null;
    vehicleToView.value = null;
    vehicleDelete.value = null;
});

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <DescriptionPage title="Gerenciamento de Veículos" description="Gerencie veículos e histórico de manutenção" />
        <div class="flex flex-row gap-2 p-4">
            <Card class="w-[25%]">
                <CardContent class="flex flex-row">
                    <div class="flex flex-col pt-3 gap-2 w-[80%]">
                        <span>Pendentes</span>
                        <span class="text-2xl font-bold">150</span>
                    </div>
                    <div class="flex items-center justify-center w-[20%]">
                        <div class="bg-blue-100 p-2 rounded-2xl mt-5 ml-4">
                            <History class="w-8 h-8 text-blue-500 dark:text-white" />
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card class="w-[25%]">
                <CardContent class="flex flex-row">
                    <div class="flex flex-col pt-3 gap-2 w-[80%]">
                        <span>Em Manutenção</span>
                        <span class="text-2xl font-bold">150</span>
                    </div>
                    <div class="flex items-center justify-center w-[20%]">
                        <div class="bg-zinc-100 p-2 rounded-2xl mt-5 ml-4">
                            <WrenchIcon class="w-8 h-8 text-zinc-500 dark:text-white" />
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card class="w-[25%]">
                <CardContent class="flex flex-row">
                    <div class="flex flex-col pt-3 gap-2 w-[80%]">
                        <span>Orçamento a Aprovar</span>
                        <span class="text-2xl font-bold">400</span>
                    </div>
                    <div class="flex items-center justify-center w-[20%]">
                        <div class="bg-yellow-100 p-2 rounded-2xl mt-5 ml-4">
                            <CircleAlertIcon class="w-8 h-8 text-yellow-500 dark:text-white" />
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card class="w-[25%]">
                <CardContent class="flex flex-row">
                    <div class="flex flex-col pt-3 gap-2 w-[80%]">
                        <span>Orçamento Aprovado</span>
                        <span class="text-2xl font-bold">150</span>
                    </div>
                    <div class="flex items-center justify-center w-[20%]">
                        <div class="bg-green-100 p-2 rounded-2xl mt-5 ml-4">
                            <CheckCircle class="w-8 h-8 text-green-500 dark:text-white" />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
        <Table :columns="columns" @create="showCreate = true" ref="tableComponent" />

        <!-- Modals -->
        <Create :show="showCreate" @update:show="showCreate = $event" @created="refreshTable" />
        <Update :show="showUpdate" @update:show="showUpdate = $event" :vehicle-data="vehicleToEdit"
            @updated="refreshTable" />
        <Info :show="showInfo" @update:show="showInfo = $event" :vehicle="vehicleToView" />
    </AppLayout>
</template>