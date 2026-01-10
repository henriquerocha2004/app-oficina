<script setup lang="ts">
import { ref, onBeforeUnmount } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';
import Table from './Table/Index.vue';
import Create from './Create.vue';
import Update from './Update.vue';
import Info from './Info.vue';
import AdjustStock from './AdjustStock.vue';
import type { ProductInterface } from './types';

const tableRef = ref<InstanceType<typeof Table>>();
const showCreate = ref(false);
const showUpdate = ref(false);
const showInfo = ref(false);
const showAdjustStock = ref(false);
const productToEdit = ref<ProductInterface | null>(null);
const productToView = ref<ProductInterface | null>(null);
const productToAdjust = ref<ProductInterface | null>(null);

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Produtos', href: '/products' },
];

const handleCreate = () => {
    showCreate.value = true;
};

const handleEdit = (product: ProductInterface) => {
    productToEdit.value = product;
    showUpdate.value = true;
};

const handleView = (product: ProductInterface) => {
    productToView.value = product;
    showInfo.value = true;
};

const handleAdjustStock = (product: ProductInterface) => {
    productToAdjust.value = product;
    showAdjustStock.value = true;
};

const handleRefresh = () => {
    tableRef.value?.refresh();
};

const handleCreated = () => {
    handleRefresh();
};

const handleUpdated = () => {
    productToEdit.value = null;
    handleRefresh();
};

const handleAdjustStockSuccess = () => {
    productToAdjust.value = null;
    handleRefresh();
};

const handleCloseInfo = () => {
    showInfo.value = false;
    productToView.value = null;
};

onBeforeUnmount(() => {
    showCreate.value = false;
    showUpdate.value = false;
    showAdjustStock.value = false;
    showInfo.value = false;
    productToEdit.value = null;
    productToView.value = null;
    productToAdjust.value = null;
});
</script>

<template>
    <AppLayout title="Produtos" :breadcrumbs="breadcrumbs">
        <DescriptionPage 
            title="Gerenciamento de Produtos"
            description="Gerencie o estoque de produtos e peças da oficina"
        />
        <Table
            ref="tableRef"
            @create="handleCreate"
            @edit="handleEdit"
            @view="handleView"
            @adjust-stock="handleAdjustStock"
        />

        <Create
            v-if="showCreate"
            :show="showCreate"
            @update:show="showCreate = $event"
            @created="handleCreated"
        />

        <Update
            v-if="productToEdit"
            :show="showUpdate"
            :product-data="productToEdit"
            @update:show="showUpdate = $event"
            @updated="handleUpdated"
        />

        <Info
            v-if="productToView"
            :show="showInfo"
            :product="productToView"
            @close="handleCloseInfo"
        />

        <AdjustStock
            v-if="productToAdjust"
            :show="showAdjustStock"
            :product-data="productToAdjust"
            @update:show="showAdjustStock = $event"
            @adjusted="handleAdjustStockSuccess"
        />
    </AppLayout>
</template>
