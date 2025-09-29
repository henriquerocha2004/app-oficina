<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { provide, ref } from 'vue';
import Create from './Create.vue';
import Table from './Table/Index.vue';
import { columns } from './Table/columns';
import { ClientInterface } from './client';
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

const data: ClientInterface[] = [
    {
        id: '1',
        name: 'John Doe',
        email: 'john@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '2',
        name: 'Jane Smith',
        email: 'jane@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '3',
        name: 'Alice Johnson',
        email: 'alice@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '4',
        name: 'Bob Brown',
        email: 'bob@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '5',
        name: 'Charlie Davis',
        email: 'charlie@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '6',
        name: 'Diana Evans',
        email: 'diana@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '7',
        name: 'Frank Green',
        email: 'frank@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '8',
        name: 'Grace Harris',
        email: 'grace@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '9',
        name: 'Henry Irving',
        email: 'henry@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '10',
        name: 'Ivy Jackson',
        email: 'ivy@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '11',
        name: 'Jack King',
        email: 'jack@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
    {
        id: '12',
        name: 'Kathy Lee',
        email: 'kathy@example.com',
        document: '12345678901',
        phone: '(11) 98765-4321',
        address: '123 Main St',
        city: 'São Paulo',
        state: 'SP',
        zipcode: '01234-567',
        observations: 'Client since 2020',
    },
];

const showCreate = ref<boolean>(false);
const showUpdate = ref<boolean>(false);
const showInfo = ref<boolean>(false);
const showDelete = ref<boolean>(false);
const clientToEdit = ref<ClientInterface | null>(null);
const clientDelete = ref<ClientInterface | null>(null);

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

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Table :columns="columns" :data="data" @create="showCreate = true" />
        <Create :show="showCreate" @update:show="showCreate = $event" />
        <Update :show="showUpdate" @update:show="showUpdate = $event" :client-data="clientToEdit" />
        <Info :show="showInfo" @update:show="showInfo = $event" />
        <Delete :show="showDelete" @update:show="showDelete = $event" :client="clientDelete" />
    </AppLayout>
</template>
