<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { provide, ref } from 'vue';
import Create from './Create.vue';
import Table from './Table/Index.vue';
import { columns } from './Table/columns';
import { ClientInterface } from './types';
import Update from './Update.vue';
import Info from './Info.vue';
import Delete from './Delete.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Clientes',
        href: "",
    },
];

const showCreate = ref<boolean>(false);
const showUpdate = ref<boolean>(false);
const showInfo = ref<boolean>(false);
const showDelete = ref<boolean>(false);
const clientToEdit = ref<ClientInterface | null>(null);
const clientDelete = ref<ClientInterface | null>(null);
const tableComponent = ref<InstanceType<typeof Table> | null>(null);

provide('onEditClient', (client: ClientInterface) => {
    console.log('Editar cliente:', client);
    clientToEdit.value = client;
    showUpdate.value = true;
});
provide('onViewClient', (client: ClientInterface) => {
    clientToEdit.value = client;
    showInfo.value = true;
});

provide('onDeleteClient', (client: ClientInterface) => {
    clientDelete.value = client;
    showDelete.value = true;
});

function refreshTable() {
    tableComponent.value?.fetchClients();
}


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Table :columns="columns" @create="showCreate = true" ref="tableComponent" />
        <Create :show="showCreate" @update:show="showCreate = $event" @created="refreshTable" />
        <Update :show="showUpdate" @update:show="showUpdate = $event" :client-data="clientToEdit"
            @updated="refreshTable" />
        <Info :show="showInfo" @update:show="showInfo = $event" :client="clientToEdit" />
        <Delete :show="showDelete" @update:show="showDelete = $event" :client="clientDelete" @deleted="refreshTable" />
    </AppLayout>
</template>
