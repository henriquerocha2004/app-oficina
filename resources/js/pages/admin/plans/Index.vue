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
import type { SubscriptionPlan } from './types';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: admin.dashboard.url(),
    },
    {
        title: 'Planos de Assinatura',
        href: '',
    },
];

const showCreate = ref(false);
const showUpdate = ref(false);
const showDelete = ref(false);
const planToEdit = ref<SubscriptionPlan | null>(null);
const planToDelete = ref<SubscriptionPlan | null>(null);
const tableComponent = ref<InstanceType<typeof Table> | null>(null);

provide('onEditPlan', (plan: SubscriptionPlan) => {
    planToEdit.value = plan;
    showUpdate.value = true;
});

provide('onDeletePlan', (plan: SubscriptionPlan) => {
    planToDelete.value = plan;
    showDelete.value = true;
});

function refreshTable() {
    tableComponent.value?.fetchPlans();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <DescriptionPage
            title="Planos de Assinatura"
            description="Gerencie os planos de assinatura disponíveis para as oficinas"
        />
        <Table :columns="columns" @create="showCreate = true" ref="tableComponent" />
        <Create :show="showCreate" @update:show="showCreate = $event" @created="refreshTable" />
        <Update
            :show="showUpdate"
            @update:show="showUpdate = $event"
            :plan-data="planToEdit"
            @updated="refreshTable"
        />
        <Delete
            :show="showDelete"
            @update:show="showDelete = $event"
            :plan="planToDelete"
            @deleted="refreshTable"
        />
    </AppLayout>
</template>
