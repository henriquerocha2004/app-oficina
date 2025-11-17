import { mount, flushPromises } from '@vue/test-utils'
import Form from '@/pages/vehicles/Form.vue'
import { vi, describe, it, expect, beforeEach } from 'vitest'

import { ref } from 'vue'

// Mock the clients search composable
vi.mock('@/pages/vehicles/composables/useClientsSearch', () => ({
  useClientsSearch: () => ({
    clients: ref([
      { id: '1', name: 'João Silva', document: '123.456.789-00' },
      { id: '2', name: 'Maria Santos', document: '987.654.321-00' }
    ]),
    isLoading: ref(false),
    searchClients: vi.fn(),
    getClientById: vi.fn(),
    clearSearch: vi.fn(),
    setClient: vi.fn(),
    minSearchLength: 3,
  })
}))

// Helper to find input by placeholder text
function findInputByPlaceholder(wrapper: any, placeholder: string) {
    return wrapper.findAll('input').find((w: any) => w.attributes('placeholder') === placeholder)
}

// Helper to find select by placeholder text
function findSelectByPlaceholder(wrapper: any, placeholder: string) {
    const selectValues = wrapper.findAll('[data-placeholder]')
    return selectValues.find((w: any) => w.attributes('data-placeholder') === placeholder)
}

describe('Vehicles Form.vue', () => {
    beforeEach(() => {
        vi.useRealTimers()
    })

    it('renders empty fields in create mode and emits submitted with normalized payload when valid', async () => {
        const wrapper = mount(Form)

        // Fill required fields manually through form values
        await (wrapper.vm as any).form.setValues({
            clientId: '1',
            brand: 'Honda',
            model: 'Civic',
            year: 2022,
            licensePlate: 'ABC1234',
            type: 'car',
            color: 'Azul',
            displacement: '2.0',
            mileage: 25000,
        })

        // Validate and submit
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
        expect(payload.data).toMatchObject({
            brand: 'Honda',
            model: 'Civic',
            year: 2022,
            licensePlate: 'ABC1234',
            color: 'Azul',
            displacement: '2.0',
            mileage: 25000,
        })
    })

    it('fills fields when editing (props.vehicle) and submits with edit mode', async () => {
        const mockVehicle = {
            id: '1',
            clientId: '1',
            brand: 'Toyota',
            model: 'Corolla',
            year: 2020,
            licensePlate: 'XYZ9876',
            color: 'Branco',
            type: 'car' as const,
            displacement: '1.8',
            fuel: 'gasoline' as const,
            transmission: 'automatic' as const,
            mileage: 50000,
            chassis: '123456789',
            observations: 'Veículo em bom estado',
        }

        const wrapper = mount(Form, {
            props: {
                vehicle: mockVehicle,
            },
        })

        await flushPromises()
        
        // Wait for form to be populated
        await wrapper.vm.$nextTick()
        await flushPromises()

        // Check if fields are filled
        const brandInput = findInputByPlaceholder(wrapper, 'Ex: Toyota')!
        expect((brandInput.element as HTMLInputElement).value).toBe('Toyota')

        const modelInput = findInputByPlaceholder(wrapper, 'Ex: Corolla')!
        expect((modelInput.element as HTMLInputElement).value).toBe('Corolla')

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
        expect(payload.data.brand).toBe('Toyota')
        expect(payload.data.model).toBe('Corolla')
        expect(payload.data.displacement).toBe('1.8')
    })

    it('formats license plate correctly', async () => {
        const wrapper = mount(Form)
        
        // Test formatLicensePlate method directly
        const result1 = (wrapper.vm as any).formatLicensePlate('abc1234')
        expect(result1).toBe('ABC1234')
        
        const result2 = (wrapper.vm as any).formatLicensePlate('abc1d23')
        expect(result2).toBe('ABC1D23')
        
        const result3 = (wrapper.vm as any).formatLicensePlate('abc-1234')
        expect(result3).toBe('ABC1234')
    })

    it('validates required fields correctly', async () => {
        const wrapper = mount(Form)

        // Submit without filling required fields
        await (wrapper.vm as any).form.validate()
        
        // Check if validation errors are shown
        const errors = wrapper.findAll('.text-destructive')
        expect(errors.length).toBeGreaterThan(0)
    })

    it('validates license plate format', async () => {
        const wrapper = mount(Form)
        
        const licensePlateInput = findInputByPlaceholder(wrapper, 'ABC1234')!
        
        // Invalid format
        await licensePlateInput.setValue('123ABCD')
        await (wrapper.vm as any).form.validate()
        
        // Should have validation error
        const errors = wrapper.findAll('.text-destructive')
        const hasLicensePlateError = errors.some(error => 
            error.text().includes('Formato de placa inválido')
        )
        expect(hasLicensePlateError).toBe(true)
    })

    it('validates year range correctly', async () => {
        const wrapper = mount(Form)
        await flushPromises()
        await wrapper.vm.$nextTick()
        
        const yearInput = findInputByPlaceholder(wrapper, 'Ex: 2020')!
        
        // Invalid year (too old)
        await yearInput.setValue('1800')
        await flushPromises()
        await (wrapper.vm as any).form.validate()
        await flushPromises()
        
        let errors = wrapper.findAll('.text-destructive')
        let hasYearError = errors.some(error => 
            error.text().includes('Ano deve ser maior que 1900')
        )
        expect(hasYearError).toBe(true)
        
        // Invalid year (future)
        const futureYear = new Date().getFullYear() + 2
        await yearInput.setValue(futureYear.toString())
        await flushPromises()
        await (wrapper.vm as any).form.validate()
        await flushPromises()
        
        errors = wrapper.findAll('.text-destructive')
        hasYearError = errors.some(error => 
            error.text().includes('Ano deve ser menor ou igual')
        )
        expect(hasYearError).toBe(true)
    }, 10000)

    it('clear() resets the form and mode to create', async () => {
        const mockVehicle = {
            id: '1',
            clientId: '1',
            brand: 'Toyota',
            model: 'Corolla',
            year: 2020,
            licensePlate: 'XYZ9876',
            color: 'Branco',
            type: 'car' as const,
            displacement: '1.8',
            observations: 'Test',
        }

        const wrapper = mount(Form, {
            props: {
                vehicle: mockVehicle,
            },
        })

        await flushPromises()

        // Call exposed method
        ;(wrapper.vm as any).clear()
        await flushPromises()

        const brandInput = findInputByPlaceholder(wrapper, 'Ex: Toyota')!
        expect((brandInput.element as HTMLInputElement).value).toBe('')
    })

    it('normalizes form data correctly', async () => {
        const wrapper = mount(Form)

        // Fill form with data
        await findInputByPlaceholder(wrapper, 'Ex: Toyota')!.setValue('honda')
        await findInputByPlaceholder(wrapper, 'Ex: Corolla')!.setValue('civic')
        await findInputByPlaceholder(wrapper, 'Ex: 2020')!.setValue('2021')
        await findInputByPlaceholder(wrapper, 'ABC1234')!.setValue('def5678')

        // Access normalizeFormData method (would need to be exposed or tested indirectly)
        const formData = {
            clientId: '1',
            brand: 'honda',
            model: 'civic',
            year: 2021,
            licensePlate: 'def5678',
            color: 'verde',
            type: 'car',
            displacement: '1.6',
            fuel: 'gasoline',
            transmission: 'manual',
            mileage: 30000,
            chassis: '987654321',
            observations: 'Teste',
        }

        const normalized = (wrapper.vm as any).normalizeFormData(formData)
        
        expect(normalized.licensePlate).toBe('DEF5678') // Should be uppercase
        expect(normalized.displacement).toBe('1.6')
        expect(normalized.fuel).toBe('gasoline')
        expect(normalized.mileage).toBe(30000)
    })
})