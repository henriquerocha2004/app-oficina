export interface SupplierInterface {
    id?: string;
    name: string;
    trade_name?: string;
    document_number: string;
    email?: string;
    phone?: string;
    mobile?: string;
    website?: string;
    street?: string;
    number?: string;
    complement?: string;
    neighborhood?: string;
    city?: string;
    state?: string;
    zip_code?: string;
    contact_person?: string;
    payment_term_days?: number;
    notes?: string;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface SuppliersResult {
    items: SupplierInterface[];
    total_items: number;
}

export interface SupplierFilterSearchResponse {
    suppliers: SuppliersResult;
}
