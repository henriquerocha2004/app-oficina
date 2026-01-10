import formatDocument from "@/utils/formatDocument";
import { assert, it } from "vitest";

it("formats a valid CPF correctly", () => {
    const rawCPF = "92082879046";
    const formattedCPF = formatDocument(rawCPF);
    assert.equal(formattedCPF, "920.828.790-46");
});

it("formats a valid CNPJ correctly", () => {
    const rawCNPJ = "58900972000112";
    const formattedCNPJ = formatDocument(rawCNPJ);
    assert.equal(formattedCNPJ, "58.900.972/0001-12");
});

it("returns the original document if it's neither a valid CPF nor CNPJ", () => {
    const invalidDocument = "123456789";
    const result = formatDocument(invalidDocument);
    assert.equal(result, invalidDocument);
});