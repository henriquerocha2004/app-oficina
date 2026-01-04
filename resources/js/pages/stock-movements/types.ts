export interface StockMovementInterface {
    id: string;
    product_id: string;
    movement_type: 'in' | 'out';
    quantity: number;
    balance_after: number;
    reference_type?: string;
    reference_id?: string;
    reason: 'purchase' | 'sale' | 'adjustment' | 'loss' | 'return' | 'transfer' | 'initial' | 'other';
    notes?: string;
    user_id?: string;
    created_at: string;
    updated_at: string;
}

export interface StockMovementDetailInterface extends StockMovementInterface {
    product_name?: string;
    product_sku?: string;
    user_name?: string;
}

export interface StockMovementsResult {
    data: StockMovementDetailInterface[];
    total: number;
}

export interface StockMovementFilters {
    product_id?: string;
    movement_type?: 'in' | 'out';
    reason?: string;
    user_id?: string;
    date_from?: string;
    date_to?: string;
}
