export interface ClientInterface {
    id: string;
    name: string;
    email: string;
    document: string;
    phone: string;
    address: string;
    city: string;
    state: string;
    zipcode: string | undefined;
    observations: string;
}
