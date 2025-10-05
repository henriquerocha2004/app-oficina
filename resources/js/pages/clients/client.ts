export interface ClientInterface {
    id: string;
    name: string;
    email: string;
    document: string;
    phone: Phone;
    address: Address;
    observations: string;
}

export interface Address {
    address: string;
    city: string;
    state: string;
    zipcode: string | undefined;
}

export interface Phone {
    number: string;
}

export interface ClientFilterSearchResponse {
    totalItems: number;
    items: ClientInterface[];
}