import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import ClientForm from "@/pages/clients/Form.vue";
import { ClientInterface } from "@/pages/clients/types";

const getValue = (wrapper: any, sel: string) =>
    (wrapper.get(sel).element as HTMLInputElement | HTMLTextAreaElement).value

describe("Client Form Page", () => {
    it("should show empty form when creating a new client", () => {
        const wrapper = mount(ClientForm, {
            props: { clientId: null, show: true },
        });
        expect(getValue(wrapper, 'input[name="name"]')).toBe("");
        expect(getValue(wrapper, 'input[name="phone"]')).toBe("");
        expect(getValue(wrapper, 'input[name="document"]')).toBe("");
        expect(getValue(wrapper, 'input[name="address"]')).toBe("");
        expect(getValue(wrapper, 'input[name="email"]')).toBe("");
        expect(getValue(wrapper, 'input[name="zipcode"]')).toBe("");
        expect(getValue(wrapper, 'input[name="city"]')).toBe("");
        expect(getValue(wrapper, 'select[name="state"]')).toBe("");
        expect(getValue(wrapper, 'textarea[name="observations"]')).toBe("");
    });

    it("should validate required name when user clean the field", async () => {
        const wrapper = mount(ClientForm, {
            props: { clientId: null, show: true },
        });

        const input = wrapper.get('input[placeholder="Nome"]');
        await input.setValue(''); // limpa o campo
        await (wrapper.vm as any).form.validate();
        expect(wrapper.text()).toContain("Nome deve ter pelo menos 3 caracteres");
        expect(wrapper.text()).toContain("Informe o telefone");
        expect(wrapper.text()).toContain("Informe o CPF/CNPJ");
        expect(wrapper.text()).toContain("Informe o email");
        expect(wrapper.text()).toContain("Selecione o estado");
    });

    it("should emit correctly data when form is valid and submitted", async () => {
        const wrapper = mount(ClientForm, {
            props: { clientId: null, show: true },
        });
        await wrapper.get('input[placeholder="Nome"]').setValue('Cliente Teste');
        await wrapper.get('input[placeholder="Telefone"]').setValue('11999999999');
        await wrapper.get('input[placeholder="CPF/CNPJ"]').setValue('92082879046');
        await wrapper.get('input[placeholder="Email"]').setValue('cliente@teste.com');
        await (wrapper.vm as any).form.setFieldValue('state', 'SP');

        await (wrapper.vm as any).form.validate();
        await (wrapper.vm as any).onSubmit();
        expect(wrapper.emitted()).toHaveProperty('submitted');
        const submittedData = wrapper.emitted('submitted')?.[0][0];
        expect(submittedData).toEqual({
            mode: 'create',
            data: {
                name: 'Cliente Teste',
                phone: '11999999999',
                document: '92082879046',
                email: 'cliente@teste.com',
                address: {
                    street: '',
                    city: '',
                    state: 'SP',
                    zipCode: "",
                },
                observations: '',
            },
        });
    });

    it("should insert data when editing an existing client", async () => {
        const existingClient: ClientInterface = {
            id: '1',
            name: 'Cliente Existente',
            email: 'cliente@existente.com',
            document: '58900972000112',
            phone: { number: '11988887777' },
            address: {
                street: 'Rua Exemplo, 123',
                city: 'São Paulo',
                state: 'SP',
                zipCode: '01234000',
            },
            observations: 'Cliente VIP',
        };
        
        const wrapper = mount(ClientForm, {
            props: { client: existingClient, show: true },
        });
        
        // Aguarda o onMounted e fillValues serem executados
        await wrapper.vm.$nextTick();
        await new Promise(resolve => setTimeout(resolve, 100));
        
        expect((wrapper.vm as any).form.values).toEqual({
            name: 'Cliente Existente',
            phone: '11988887777',
            document: '58900972000112',
            email: 'cliente@existente.com',
            address: 'Rua Exemplo, 123',
            city: 'São Paulo',
            state: 'SP',
            zipcode: '01234000',
            observations: 'Cliente VIP',
        });

        expect((wrapper.vm as any).mode).toBe('edit');
    }, 10000); // Aumenta o timeout para 10 segundos
});