import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Service {
    id: string;
    name: string;
    description: string | null;
    base_price: number;
    category: 'maintenance' | 'repair' | 'diagnostic' | 'painting' | 'alignment' | 'other';
    estimated_time: number | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface ServiceFormData {
    name: string;
    description?: string;
    base_price: number | string;
    category: string;
    estimated_time?: number | string;
    is_active: boolean;
}
