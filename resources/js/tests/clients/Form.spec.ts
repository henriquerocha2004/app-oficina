import { mount, flushPromises } from '@vue/test-utils'
import Form from '@/pages/clients/Form.vue'
import { vi, describe, it, expect, beforeEach } from 'vitest'

// Mock cpf-cnpj-validator to simplify document validation
vi.mock('cpf-cnpj-validator', () => {
    return {
        cpf: { isValid: vi.fn().mockReturnValue(true) },
        cnpj: { isValid: vi.fn().mockReturnValue(false) },
    }
})

// Helper to find input by placeholder text
function findInputByPlaceholder(wrapper: any, placeholder: string) {
    return wrapper.findAll('input').find((w: any) => w.attributes('placeholder') === placeholder)
}

describe('Clients Form.vue', () => {
    // no-op

    beforeEach(() => {
        vi.useRealTimers()
    })
    it('renders empty fields in create mode and emits submitted with normalized payload when valid', async () => {
        const wrapper = mount(Form)

        // Fill required fields (use raw phone as in page tests)
        await findInputByPlaceholder(wrapper, 'Nome')!.setValue('João da Silva')
        await findInputByPlaceholder(wrapper, 'Telefone')!.setValue('11987654321')
        await findInputByPlaceholder(wrapper, 'CPF/CNPJ')!.setValue('11144477735')
        await findInputByPlaceholder(wrapper, 'Email')!.setValue('joao@example.com')

        // Optional fields
        await findInputByPlaceholder(wrapper, 'CEP')!.setValue('01001-000')
        await findInputByPlaceholder(wrapper, 'Endereço')!.setValue('Praça da Sé')
        await findInputByPlaceholder(wrapper, 'Cidade')!.setValue('São Paulo')

        // Validate and submit using component methods (as in existing page tests)
        await (wrapper.vm as any).form.validate()
        await (wrapper.vm as any).onSubmit()
        await flushPromises()

        const emitted = wrapper.emitted('submitted') as unknown as Array<[
            {
                mode: 'create' | 'edit';
                data: any;
            }
        ]>
        expect(emitted).toBeTruthy()
        const payload = emitted![0][0]
        expect(payload.mode).toBe('create')
        expect(payload.data).toEqual({
            name: 'João da Silva',
            phone: '11987654321',
            document_number: '11144477735',
            email: 'joao@example.com',
            street: 'Praça da Sé',
            city: 'São Paulo',
            state: '',
            zip_code: '01001-000',
            observations: '',
        })
    })

    it('fills fields when editing (props.client) and submits with edit mode', async () => {
        const wrapper = mount(Form, {
            props: {
                client: {
                    id: '01HZK5ABCDEF',
                    name: 'Maria Souza',
                    email: 'maria@example.com',
                    document_number: '12345678901',
                    phone: '11987654321',
                    street: 'Rua A',
                    city: 'SP',
                    state: 'SP',
                    zip_code: '02000-000',
                    observations: 'VIP',
                },
            },
        })

        await flushPromises()

        // Submit without changing
        await (wrapper.vm as any).form.validate()
        await (wrapper.vm as any).onSubmit()
        await flushPromises()

        const emitted = wrapper.emitted('submitted') as unknown as Array<[{
            mode: 'create' | 'edit';
            data: any;
        }]>
        expect(emitted).toBeTruthy()
        const payload = emitted![0][0]
        expect(payload.mode).toBe('edit')
        expect(payload.data.name).toBe('Maria Souza')
        expect(payload.data.street).toBe('Rua A')
    })

    it('fetches address by zipcode (ViaCEP) and updates address/city/state', async () => {
        // Mock fetch
        global.fetch = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ logradouro: 'Praça da Sé', localidade: 'São Paulo', uf: 'SP' }),
        }) as any

        vi.useFakeTimers()

        const wrapper = mount(Form)
        const zipcode = findInputByPlaceholder(wrapper, 'CEP')!
        await zipcode.setValue('01001-000')

        // Debounce 500ms
        await vi.advanceTimersByTimeAsync(500)
        await flushPromises()

        const address = findInputByPlaceholder(wrapper, 'Endereço')!
        const city = findInputByPlaceholder(wrapper, 'Cidade')!

        expect((address.element as HTMLInputElement).value).toBe('Praça da Sé')
        expect((city.element as HTMLInputElement).value).toBe('São Paulo')
    })

    it('clear() resets the form and mode to create', async () => {
        const wrapper = mount(Form, {
            props: {
                client: {
                    id: '01HZK5ABCDEF',
                    name: 'Alguém',
                    email: 'a@a.com',
                    document_number: '12345678901',
                    phone: '11999999999',
                    address: { street: 'R1', city: 'C1', state: 'SP', zipCode: '02000-000' },
                    observations: 'O1',
                },
            },
        })

            // call exposed method
            ; (wrapper.vm as any).clear()
        await flushPromises()

        const name = findInputByPlaceholder(wrapper, 'Nome')!
        expect((name.element as HTMLInputElement).value).toBe('')
    })
})
