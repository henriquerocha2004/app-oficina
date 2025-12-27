<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import { provide, ref } from 'vue';
import Create from './Create.vue';
import Update from './Update.vue';
import Delete from './Delete.vue';
import Table from './Table/Index.vue';
import { columns } from './Table/columns';
import type { Tenant } from './types';
import type { SubscriptionPlan } from '../plans/types';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';

interface Props {
    subscription_plans: SubscriptionPlan[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: admin.dashboard.url(),
    },
    {
        title: 'Oficinas',
        href: '',
    },
];

const showCreate = ref(false);
const showUpdate = ref(false);
const showDelete = ref(false);
const tenantToEdit = ref<Tenant | null>(null);
const tenantToDelete = ref<Tenant | null>(null);
const tableComponent = ref<InstanceType<typeof Table> | null>(null);

provide('onEditTenant', (tenant: Tenant) => {
    tenantToEdit.value = tenant;
    showUpdate.value = true;
});

provide('onDeleteTenant', (tenant: Tenant) => {
    tenantToDelete.value = tenant;
    showDelete.value = true;
});

function refreshTable() {
    tableComponent.value?.fetchTenants();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <DescriptionPage
            title="Oficinas"
            description="Gerencie as oficinas cadastradas no sistema"
        />
        <Table :columns="columns" @create="showCreate = true" ref="tableComponent" />
        <Create
            :show="showCreate"
            @update:show="showCreate = $event"
            :subscription-plans="subscription_plans"
            @created="refreshTable"
        />
        <Update
            :show="showUpdate"
            @update:show="showUpdate = $event"
            :tenant-data="tenantToEdit"
            :subscription-plans="subscription_plans"
            @updated="refreshTable"
        />
        <Delete
            :show="showDelete"
            @update:show="showDelete = $event"
            :tenant="tenantToDelete"
            @deleted="refreshTable"
        />
    </AppLayout>
</template>
