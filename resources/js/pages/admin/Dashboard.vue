<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Package } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_tenants: number;
        active_tenants: number;
        total_plans: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Painel Administrativo</h1>
                <p class="text-muted-foreground">Gerencie oficinas e planos de assinatura</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total de Oficinas</CardTitle>
                        <Building2 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_tenants }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ stats.active_tenants }} ativas
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Planos de Assinatura</CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_plans }}</div>
                        <p class="text-xs text-muted-foreground">Planos disponíveis</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card class="hover:bg-accent/50 transition-colors cursor-pointer">
                    <Link :href="admin.tenants.index.url()">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Building2 class="h-5 w-5" />
                                Gerenciar Oficinas
                            </CardTitle>
                            <CardDescription>
                                Visualize e gerencie todas as oficinas cadastradas no sistema
                            </CardDescription>
                        </CardHeader>
                    </Link>
                </Card>

                <Card class="hover:bg-accent/50 transition-colors cursor-pointer">
                    <Link :href="admin.plans.index.url()">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Package class="h-5 w-5" />
                                Planos de Assinatura
                            </CardTitle>
                            <CardDescription>
                                Configure os planos de assinatura disponíveis para as oficinas
                            </CardDescription>
                        </CardHeader>
                    </Link>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
