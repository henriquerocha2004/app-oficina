<script setup lang="ts">
import { ref, onBeforeUnmount } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';
import Table from './Table/Index.vue';
import Create from './Create.vue';
import Update from './Update.vue';
import Info from './Info.vue';
import type { SupplierInterface } from './types';

const tableRef = ref<InstanceType<typeof Table>>();
const showCreate = ref(false);
const showUpdate = ref(false);
const showInfo = ref(false);
const supplierToEdit = ref<SupplierInterface | null>(null);
const supplierToView = ref<SupplierInterface | null>(null);

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Fornecedores', href: '/suppliers' },
];

const handleCreate = () => {
    showCreate.value = true;
};

const handleEdit = (supplier: SupplierInterface) => {
    supplierToEdit.value = supplier;
    showUpdate.value = true;
};

const handleView = (supplier: SupplierInterface) => {
    supplierToView.value = supplier;
    showInfo.value = true;
};

const handleRefresh = () => {
    tableRef.value?.refresh();
};

const handleCreated = () => {
    handleRefresh();
};

const handleUpdated = () => {
    supplierToEdit.value = null;
    handleRefresh();
};

const handleCloseInfo = () => {
    showInfo.value = false;
    supplierToView.value = null;
};

onBeforeUnmount(() => {
    showCreate.value = false;
    showUpdate.value = false;
    showInfo.value = false;
    supplierToEdit.value = null;
    supplierToView.value = null;
});
</script>

<template>
    <AppLayout title="Fornecedores" :breadcrumbs="breadcrumbs">
        <DescriptionPage 
            title="Gerenciamento de Fornecedores"
            description="Gerencie os fornecedores de produtos e peças"
        />
        <Table
            ref="tableRef"
            @create="handleCreate"
            @edit="handleEdit"
            @view="handleView"
        />

        <Create
            v-if="showCreate"
            :show="showCreate"
            @update:show="showCreate = $event"
            @created="handleCreated"
        />

        <Update
            v-if="supplierToEdit"
            :show="showUpdate"
            :supplier-data="supplierToEdit"
            @update:show="showUpdate = $event"
            @updated="handleUpdated"
        />

        <Info
            v-if="supplierToView"
            :show="showInfo"
            :supplier="supplierToView"
            @close="handleCloseInfo"
        />
    </AppLayout>
</template>
