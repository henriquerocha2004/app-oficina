export interface VehiclesInterface {
    id?: string;
    clientId?: string;
    clientName?: string;
    client?: string;
    brand?: string;
    model: string;
    year: number;
    licensePlate?: string;
    color?: string;
    typeVehicle?: VehicleType;
    
    displacement?: string;
    fuel?: FuelType | string;
    transmission?: TransmissionType | string;
    mileage?: number;
    chassis?: string;
    
    observations?: string;
    
    phone?: string;
    last_service_date?: string;
    status?: string;
}

export type VehicleType = 'car' | 'motorcycle';
export type FuelType = 'alcohol' | 'gasoline' | 'diesel';
export type TransmissionType = 'manual' | 'automatic';

export interface VehiclesResult {
    totalItems: number;
    items: VehiclesInterface[];
}

export interface VehicleFilterSearchResponse {
    vehicles: VehiclesResult;
}

export interface ClientOption {
    id: string;
    name: string;
}