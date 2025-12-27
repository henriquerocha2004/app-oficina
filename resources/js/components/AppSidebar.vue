<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index } from '@/routes/clients';
import { index as indexCar } from '@/routes/vehicles';
import { index as indexServices } from '@/routes/services';
import admin from '@/routes/admin';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Banknote, BookOpen, Building2, Car, ClipboardPaste, Folder, LayoutGrid, Package, User, Wrench } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

// Detectar se estamos no painel admin
const isAdminPanel = computed(() => {
    return page.url.startsWith('/admin');
});

// Menu do painel principal (tenant)
const tenantNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clientes',
        href: index(),
        icon: User,
    },
    {
        title: 'Veiculos',
        href: indexCar(),
        icon: Car,
    },
    {
        title: 'Serviços',
        href: indexServices(),
        icon: Wrench,
    },
    {
        title: 'Ordems de Serviço',
        href: '#',
        icon: ClipboardPaste,
    },
    {
        title: 'Estoque',
        href: '#',
        icon: Package,
    },
    {
        title: 'Financeiro',
        href: '#',
        icon: Banknote,
    },
];

// Menu do painel admin
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: admin.dashboard.url(),
        icon: LayoutGrid,
    },
    {
        title: 'Planos',
        href: admin.plans.index.url(),
        icon: Package,
    },
    {
        title: 'Oficinas',
        href: admin.tenants.index.url(),
        icon: Building2,
    },
];

// Selecionar menu baseado no contexto
const mainNavItems = computed(() => {
    return isAdminPanel.value ? adminNavItems : tenantNavItems;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

// Dashboard link baseado no contexto
const dashboardLink = computed(() => {
    return isAdminPanel.value ? admin.dashboard.url() : dashboard();
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardLink">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
