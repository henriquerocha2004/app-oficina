export interface ProductInterface {
    id?: string;
    name: string;
    description?: string;
    sku?: string;
    barcode?: string;
    manufacturer?: string;
    category: 'engine' | 'suspension' | 'brakes' | 'electrical' | 'filters' | 'fluids' | 'tires' | 'body_parts' | 'interior' | 'tools' | 'other';
    stock_quantity: number;
    min_stock_level?: number;
    unit: 'unit' | 'liter' | 'kg' | 'meter' | 'box';
    unit_price: number;
    suggested_price?: number;
    is_active: boolean;
    is_low_stock?: boolean;
    suppliers?: SupplierWithPivotInterface[];
    created_at?: string;
    updated_at?: string;
}

export interface SupplierWithPivotInterface {
    id: string;
    name: string;
    supplier_sku?: string;
    cost_price: number;
    lead_time_days?: number;
    min_order_quantity: number;
    is_preferred: boolean;
    notes?: string;
}

export interface ProductsResult {
    items: ProductInterface[];
    total_items: number;
}

export interface ProductFilterSearchResponse {
    products: ProductsResult;
}

export interface AdjustStockData {
    movement_type: 'in' | 'out';
    quantity: number;
    reason: 'purchase' | 'sale' | 'adjustment' | 'loss' | 'return' | 'transfer' | 'initial' | 'other';
    notes?: string;
}

export interface StockMovementInterface {
    id: string;
    product_id: string;
    product_name?: string;
    movement_type: 'in' | 'out';
    quantity: number;
    balance_after?: number;
    reference_type?: string;
    reference_id?: string;
    reason: string;
    notes?: string;
    user_id?: string;
    user_name?: string;
    created_at?: string;
}
