export interface ClientInterface {
    id?: string;
    name: string;
    email: string;
    document: string;
    phone: Phone;
    address: Address;
    observations: string;
}

export interface Address {
    street: string;
    city: string;
    state: string;
    zipCode: string | undefined;
}

export interface Phone {
    number: string;
}

export interface ClientsResult {
    totalItems: number;
    items: ClientInterface[];
}

export interface ClientFilterSearchResponse {
    clients: ClientsResult;
}