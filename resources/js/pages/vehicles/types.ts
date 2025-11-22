export interface VehiclesInterface {
    id?: string;
    client_id?: string;
    brand: string;
    model: string;
    year: number;
    plate: string;
    color?: string;
    vehicle_type?: VehicleType;
    cilinder_capacity?: string;
    fuel?: FuelType;
    transmission?: TransmissionType;
    mileage?: number;
    vin?: string;
    observations?: string;
    client?: ClientInfo;
}

export interface ClientInfo {
    id: string;
    name: string;
    email: string;
    document_number: string;
    phone: string;
}

export interface VehiclesResult {
    total_items: number;
    items: VehiclesInterface[];
}

export interface VehicleFilterSearchResponse {
    vehicles: VehiclesResult;
}

export interface ClientOption {
    id: string;
    name: string;
    document_number: string;
}

export type VehicleType = 'car' | 'motorcycle';
export type FuelType = 'alcohol' | 'gasoline' | 'diesel';
export type TransmissionType = 'manual' | 'automatic';