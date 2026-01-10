<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DescriptionPage from '@/pages/Shared/Components/DescriptionPage.vue';
import Table from './Table/Index.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Movimentações de Estoque', href: '/stock-movements' },
];
</script>

<template>
    <AppLayout title="Movimentações de Estoque" :breadcrumbs="breadcrumbs">
        <DescriptionPage 
            title="Histórico de Movimentações"
            description="Acompanhe todas as entradas e saídas de produtos do estoque"
        />
        <Table />
    </AppLayout>
</template>
