import { assert, it } from "vitest";
import formatPhone from "@/utils/formatPhone";

it("should format valid phone numbers correctly", () => {
    const testCases = [
        { input: "11987654321", expected: "(11) 98765-4321" },
        { input: "1187654321", expected: "(11) 8765-4321" },
        { input: "(11) 98765-4321", expected: "(11) 98765-4321" },
        { input: "(11) 8765-4321", expected: "(11) 8765-4321" },
        { input: "11-98765-4321", expected: "(11) 98765-4321" },
        { input: "11-8765-4321", expected: "(11) 8765-4321" },
        { input: "1198765432100", expected: "(11) 98765-4321" },
    ];

    testCases.forEach(({ input, expected }) => {
        const formattedPhone = formatPhone(input);
        assert.equal(formattedPhone, expected);
    });
});