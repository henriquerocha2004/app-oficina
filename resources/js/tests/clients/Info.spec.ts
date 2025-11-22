import { mount, flushPromises } from '@vue/test-utils'
import Info from '@/pages/clients/Info.vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
// no-op

vi.mock('@/utils/formatPhone', () => ({
    default: (v: string) => `phone(${v})`,
}))

vi.mock('@/utils/formatDocument', () => ({
    default: (v: string) => `doc(${v})`,
}))

vi.mock('@/api/Vehicles', () => ({
    VehiclesApi: {
        getByClientId: vi.fn(),
    },
}))

// We'll stub Drawer components via global stubs instead of module mocking to avoid hoisting issues

describe('Clients Info.vue', () => {
    const client = {
        id: '01ABC',
        name: 'John Doe',
        email: 'john@example.com',
        document_number: '12345678901',
        phone: '11987654321',
        street: 'Rua X',
        city: 'SP',
        state: 'SP',
        zip_code: '01000-000',
        observations: 'Obs',
    }

    beforeEach(() => {
        vi.clearAllMocks()
    })

    it('renders client details with formatted phone and document', () => {
        const wrapper = mount(Info, {
            props: { show: true, client },
            global: {
                stubs: {
                    Drawer: { template: '<div data-test="drawer-root"><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('John Doe')
        expect(wrapper.text()).toContain('john@example.com')
        expect(wrapper.text()).toContain('phone(11987654321)')
        expect(wrapper.text()).toContain('doc(12345678901)')
        expect(wrapper.text()).toContain('Rua X')
        expect(wrapper.text()).toContain('01000-000')
    })

    it('emits update:show when Drawer update:open is fired', async () => {
        const wrapper = mount(Info, {
            props: { show: true, client },
            global: {
                stubs: {
                    Drawer: { template: '<div data-test="drawer-root" @click="$emit(\'update:open\', false)"><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })
        const drawer = wrapper.get('[data-test="drawer-root"]')
        await drawer.trigger('click')
        expect(wrapper.emitted('update:show')).toBeTruthy()
        const [[value]] = wrapper.emitted('update:show') as any
        expect(typeof value).toBe('boolean')
    })

    it('loads vehicles when drawer is shown', async () => {
        const { VehiclesApi } = await import('@/api/Vehicles')
        const mockVehicles = [
            {
                id: '1',
                brand: 'Toyota',
                model: 'Corolla',
                year: 2020,
                plate: 'ABC1234',
                vehicle_type: 'car' as const,
                client_id: '01ABC',
                color: 'Black',
            },
            {
                id: '2',
                brand: 'Honda',
                model: 'Civic',
                year: 2021,
                plate: 'XYZ9876',
                vehicle_type: 'car' as const,
                client_id: '01ABC',
                color: 'White',
            },
        ]

        vi.mocked(VehiclesApi.getByClientId).mockResolvedValue({ vehicles: mockVehicles })

        const wrapper = mount(Info, {
            props: { show: false, client },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                    Car: { template: '<div class="car-icon"></div>' },
                }
            }
        })

        // Abre o drawer
        await wrapper.setProps({ show: true })
        await flushPromises()

        expect(VehiclesApi.getByClientId).toHaveBeenCalledWith('01ABC')
        expect(wrapper.text()).toContain('Toyota Corolla')
        expect(wrapper.text()).toContain('Honda Civic')
        expect(wrapper.text()).toContain('ABC1234')
        expect(wrapper.text()).toContain('XYZ9876')
    })

    it('displays "Nenhum veículo cadastrado" when client has no vehicles', async () => {
        const { VehiclesApi } = await import('@/api/Vehicles')
        vi.mocked(VehiclesApi.getByClientId).mockResolvedValue({ vehicles: [] })

        const wrapper = mount(Info, {
            props: { show: false, client },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        await wrapper.setProps({ show: true })
        await flushPromises()

        expect(wrapper.text()).toContain('Nenhum veículo cadastrado')
    })

    it('displays loading state while fetching vehicles', async () => {
        const { VehiclesApi } = await import('@/api/Vehicles')
        let resolvePromise: any
        vi.mocked(VehiclesApi.getByClientId).mockImplementation(() => new Promise(resolve => { resolvePromise = resolve }))

        const wrapper = mount(Info, {
            props: { show: false, client },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        await wrapper.setProps({ show: true })
        await flushPromises()

        expect(wrapper.text()).toContain('Carregando veículos')
        
        // Resolve a promise para limpar
        resolvePromise({ vehicles: [] })
        await flushPromises()
    })

    it('handles API errors gracefully', async () => {
        const { VehiclesApi } = await import('@/api/Vehicles')
        const consoleSpy = vi.spyOn(console, 'error').mockImplementation(() => {})
        vi.mocked(VehiclesApi.getByClientId).mockRejectedValue(new Error('API Error'))

        const wrapper = mount(Info, {
            props: { show: false, client },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        await wrapper.setProps({ show: true })
        await flushPromises()

        expect(consoleSpy).toHaveBeenCalledWith('Error loading vehicles:', expect.any(Error))
        expect(wrapper.text()).toContain('Nenhum veículo cadastrado')
        
        consoleSpy.mockRestore()
    })
})
