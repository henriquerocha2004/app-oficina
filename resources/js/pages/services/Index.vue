<script setup lang="ts">
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { provide, ref } from 'vue';
import { Service } from '@/types';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import ServicesTable from './components/ServicesTable.vue';
import ServiceFormDialog from './components/ServiceFormDialog.vue';
import ServiceInfoDialog from './components/ServiceInfoDialog.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Serviços',
        href: '',
    },
];

const showCreate = ref<boolean>(false);
const showUpdate = ref<boolean>(false);
const showInfo = ref<boolean>(false);
const serviceToEdit = ref<Service | null>(null);
const serviceToView = ref<Service | null>(null);
const tableComponent = ref<InstanceType<typeof ServicesTable> | null>(null);

provide('onEditService', (service: Service) => {
    serviceToEdit.value = service;
    showUpdate.value = true;
});

provide('onViewService', (service: Service) => {
    serviceToView.value = service;
    showInfo.value = true;
});

function refreshTable() {
    tableComponent.value?.fetchServices();
}

function handleCreateSuccess() {
    showCreate.value = false;
    refreshTable();
}

function handleUpdateSuccess() {
    showUpdate.value = false;
    serviceToEdit.value = null;
    refreshTable();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <DescriptionPage title="Gerenciamento de Serviços" description="Gerencie os serviços oferecidos pela oficina" />

        <div class="flex justify-end p-4">
            <Button @click="showCreate = true">
                <Plus class="mr-2 h-4 w-4" />
                Novo Serviço
            </Button>
        </div>

        <ServicesTable ref="tableComponent" />

        <ServiceFormDialog :open="showCreate" @update:open="showCreate = $event" @success="handleCreateSuccess"
            title="Criar Novo Serviço" />

        <ServiceFormDialog v-if="serviceToEdit" :open="showUpdate" @update:open="showUpdate = $event"
            @success="handleUpdateSuccess" :service="serviceToEdit" title="Editar Serviço" />

        <ServiceInfoDialog v-if="serviceToView" :open="showInfo" @update:open="showInfo = $event"
            :service="serviceToView" />
    </AppLayout>
</template>
