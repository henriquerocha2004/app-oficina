import { mount, flushPromises } from '@vue/test-utils'
import Info from '@/pages/vehicles/Info.vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import type { VehiclesInterface } from '@/pages/vehicles/types'

vi.mock('@/utils/formatPhone', () => ({
    default: (v: string) => `phone(${v})`,
}))

describe('Vehicles Info.vue', () => {
    const mockVehicle: VehiclesInterface = {
        id: '1',
        brand: 'Toyota',
        model: 'Corolla',
        year: 2020,
        plate: 'ABC1234',
        color: 'Prata',
        vehicle_type: 'car',
        fuel: 'gasoline',
        transmission: 'automatic',
        mileage: 45000,
        vin: '1HGBH41JXMN109186',
        observations: 'Veículo em excelente estado de conservação. Cliente muito cuidadoso.',
        cilinder_capacity: '1800',
        client_id: 'client-123',
        client: {
            id: 'client-123',
            name: 'Maria Silva',
            email: 'maria@example.com',
            document_number: '12345678901',
            phone: '11999990001',
        },
    }

    beforeEach(() => {
        vi.clearAllMocks()
    })

    it('renders vehicle details correctly', () => {
        const wrapper = mount(Info, {
            props: { show: true, vehicle: mockVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                    Wrench: { template: '<div class="wrench-icon"></div>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Toyota')
        expect(wrapper.text()).toContain('Corolla')
        expect(wrapper.text()).toContain('2020')
        expect(wrapper.text()).toContain('ABC1234')
        expect(wrapper.text()).toContain('Prata')
        expect(wrapper.text()).toContain('Carro')
    })

    it('displays vehicle technical information', () => {
        const wrapper = mount(Info, {
            props: { show: true, vehicle: mockVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Gasolina')
        expect(wrapper.text()).toContain('Automática')
        expect(wrapper.text()).toContain('45.000 km')
    })

    it('displays client/owner information', () => {
        const wrapper = mount(Info, {
            props: { show: true, vehicle: mockVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Maria Silva')
        expect(wrapper.text()).toContain('phone(11999990001)')
    })

    it('displays maintenance history with hardcoded data', () => {
        const wrapper = mount(Info, {
            props: { show: true, vehicle: mockVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                    Wrench: { template: '<div class="wrench-icon"></div>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Troca de óleo e filtros')
        expect(wrapper.text()).toContain('14/01/2024')
        expect(wrapper.text()).toContain('R$ 180,00')
        expect(wrapper.text()).toContain('Revisão dos 40.000 km')
        expect(wrapper.text()).toContain('R$ 450,00')
        expect(wrapper.text()).toContain('Troca de pastilhas de freio')
        expect(wrapper.text()).toContain('R$ 320,00')
        expect(wrapper.text()).toContain('Concluído')
    })

    it('displays observations section', () => {
        const wrapper = mount(Info, {
            props: { show: true, vehicle: mockVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Observações')
        expect(wrapper.text()).toContain('Veículo em excelente estado de conservação')
    })

    it('displays default observation when vehicle has no observations', () => {
        const vehicleWithoutObs = { ...mockVehicle, observations: undefined }
        const wrapper = mount(Info, {
            props: { show: true, vehicle: vehicleWithoutObs },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Veículo em excelente estado de conservação. Cliente muito cuidadoso.')
    })

    it('emits update:show when drawer is closed', async () => {
        const wrapper = mount(Info, {
            props: { show: true, vehicle: mockVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div data-test="drawer" @click="$emit(\'update:open\', false)"><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        const drawer = wrapper.get('[data-test="drawer"]')
        await drawer.trigger('click')
        
        expect(wrapper.emitted('update:show')).toBeTruthy()
        const [[value]] = wrapper.emitted('update:show') as any
        expect(typeof value).toBe('boolean')
    })

    it('handles vehicle type labels correctly', () => {
        const motorcycleVehicle = { ...mockVehicle, vehicle_type: 'motorcycle' as const }
        const wrapper = mount(Info, {
            props: { show: true, vehicle: motorcycleVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Moto')
    })

    it('handles fuel type labels correctly', () => {
        const dieselVehicle = { ...mockVehicle, fuel: 'diesel' as const }
        const wrapper = mount(Info, {
            props: { show: true, vehicle: dieselVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Diesel')
    })

    it('handles manual transmission label correctly', () => {
        const manualVehicle = { ...mockVehicle, transmission: 'manual' as const }
        const wrapper = mount(Info, {
            props: { show: true, vehicle: manualVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('Manual')
    })

    it('displays default mileage when vehicle has no mileage', () => {
        const vehicleWithoutMileage = { ...mockVehicle, mileage: undefined }
        const wrapper = mount(Info, {
            props: { show: true, vehicle: vehicleWithoutMileage },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        expect(wrapper.text()).toContain('45.000 km')
    })

    it('displays dash when optional fields are missing', () => {
        const minimalVehicle: VehiclesInterface = {
            id: '1',
            brand: 'Toyota',
            model: 'Corolla',
            year: 2020,
            plate: 'ABC1234',
            client_id: 'client-123',
        }
        
        const wrapper = mount(Info, {
            props: { show: true, vehicle: minimalVehicle },
            global: {
                stubs: {
                    Drawer: { template: '<div><slot /></div>' },
                    DrawerContent: { template: '<div><slot /></div>' },
                    DrawerHeader: { template: '<div><slot /></div>' },
                    DrawerTitle: { template: '<h2><slot /></h2>' },
                }
            }
        })

        const text = wrapper.text()
        expect(text).toContain('-')
    })
})
