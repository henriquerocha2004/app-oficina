export interface SubscriptionPlan {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    billing_cycle: 'monthly' | 'yearly';
    max_users: number;
    max_clients: number;
    max_vehicles: number;
    max_services_per_month: number;
    features: string[];
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface PaginatedPlans {
    data: SubscriptionPlan[];
    links: any;
    meta: any;
}
