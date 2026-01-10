export interface Tenant {
    id: string;
    name: string;
    slug: string;
    is_active: boolean;
    subscription_plan_id: string;
    created_at: string;
    updated_at: string;
    subscription_plan: {
        id: string;
        name: string;
    };
    domains: Array<{
        id: number;
        domain: string;
    }>;
}

export interface PaginatedTenants {
    data: Tenant[];
    links: any;
    meta: any;
}

export interface TenantFormData {
    name: string;
    slug: string;
    subscription_plan_id: string;
    domain: string;
    admin_name: string;
    admin_email: string;
    admin_password: string;
    is_active: boolean;
}
