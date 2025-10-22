import { cnpj, cpf } from "cpf-cnpj-validator";

export default function formatDocument(document: string): string {
    if (cpf.isValid(document)) {
        return cpf.format(document);
    }

    if (cnpj.isValid(document)) {
        return cnpj.format(document);
    }

    return document;
}    