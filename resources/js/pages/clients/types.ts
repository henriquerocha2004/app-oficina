export interface ClientInterface {
    id?: string;
    name: string;
    email: string;
    document_number: string;
    phone: string;
    street?: string;
    city?: string;
    state?: string;
    zip_code?: string;
    observations?: string;
}

export interface ClientsResult {
    total_items: number;
    items: ClientInterface[];
}

export interface ClientFilterSearchResponse {
    clients: ClientsResult;
}