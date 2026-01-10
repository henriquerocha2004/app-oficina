import { mount, flushPromises } from '@vue/test-utils'
import Update from '@/pages/vehicles/Update.vue'
import { vi, describe, it, expect, beforeEach } from 'vitest'
import type { VehiclesInterface } from '@/pages/vehicles/types'

// Mock vue-sonner
vi.mock('vue-sonner', () => ({
    toast: {
        success: vi.fn(),
        error: vi.fn(),
    },
}))

// Mock Vehicles API
vi.mock('@/api/Vehicles', () => ({
    VehiclesApi: {
        update: vi.fn()
    }
}))

import { ref } from 'vue'

// Mock clients search composable
vi.mock('@/pages/vehicles/composables/useClientsSearch', () => ({
  useClientsSearch: () => ({
    clients: ref([
      { id: '1', name: 'João Silva', document: '123.456.789-00' },
      { id: '2', name: 'Maria Santos', document: '987.654.321-00' }
    ]),
    isLoading: ref(false),
    loadClients: vi.fn().mockResolvedValue(undefined),
    searchClients: vi.fn(),
    getClientById: vi.fn(),
    clearSearch: vi.fn(),
    setClient: vi.fn(),
    minSearchLength: 3,
    initializeClients: vi.fn().mockResolvedValue(undefined),
  })
}))

describe('Vehicles Update.vue', () => {
    const mockVehicleData: VehiclesInterface = {
        id: '1',
        brand: 'Toyota',
        model: 'Corolla',
        year: 2020,
        plate: 'ABC1234',
        vehicle_type: 'car',
        color: 'Black',
        client_id: '1',
        cilinder_capacity: '1800',
        vin: '1HGBH41JXMN109186',
        observations: 'Test vehicle',
    }

    beforeEach(() => {
        vi.clearAllMocks()
    })

    it('renders with correct title when vehicle data is provided', async () => {
        const wrapper = mount(Update, {
            props: {
                show: true,
                vehicleData: mockVehicleData,
            },
        })

        expect(wrapper.vm).toBeTruthy()
        expect(wrapper.props('show')).toBe(true)
        expect(wrapper.props('vehicleData')).toEqual(mockVehicleData)
    })

    it('does not render when show is false', () => {
        const wrapper = mount(Update, {
            props: {
                show: false,
                vehicleData: mockVehicleData,
            },
        })

        const sheet = wrapper.find('[data-state="open"]')
        expect(sheet.exists()).toBe(false)
    })

    it('emits update:show when sheet is closed', async () => {
        const wrapper = mount(Update, {
            props: {
                show: true,
                vehicleData: mockVehicleData,
            },
        })

        await flushPromises()

        const sheet = wrapper.findComponent({ name: 'Sheet' })
        if (sheet.exists()) {
            await sheet.vm.$emit('update:open', false)
        }
        
        await flushPromises()
        
        expect(wrapper.emitted('update:show')).toBeTruthy()
        expect(wrapper.emitted('update:show')![0]).toEqual([false])
    })

    it('handles successful vehicle update', async () => {
        const { toast } = await import('vue-sonner')
        const { VehiclesApi } = await import('@/api/Vehicles')
        vi.mocked(VehiclesApi.update).mockResolvedValue({ status: 'success', message: 'Success', data: { id: '1' } })
        
        const wrapper = mount(Update, {
            props: {
                show: true,
                vehicleData: mockVehicleData,
            },
        })

        const mockFormData = {
            mode: 'edit',
            data: {
                client_id: '1',
                brand: 'Toyota',
                model: 'Corolla',
                year: 2021,
                plate: 'XYZ9876',
                vehicle_type: 'car',
                color: 'White',
                cilinder_capacity: '2000',
                vin: '1HGBH41JXMN109999',
                observations: 'Updated vehicle',
            },
        }

        await (wrapper.vm as any).save(mockFormData)

        expect(VehiclesApi.update).toHaveBeenCalledWith(expect.objectContaining({
            id: '1',
            brand: 'Toyota',
            model: 'Corolla',
            year: 2021,
            client_id: '1',
            plate: 'XYZ9876',
            vehicle_type: 'car',
        }))

        expect(wrapper.emitted('updated')).toBeTruthy()
        expect(wrapper.emitted('update:show')).toBeTruthy()

        expect(toast.success).toHaveBeenCalledWith(
            'Veículo atualizado com sucesso',
            { position: 'top-right' }
        )
    })

    it('does not process when mode is not edit', async () => {
        const { VehiclesApi } = await import('@/api/Vehicles')
        
        const wrapper = mount(Update, {
            props: {
                show: true,
                vehicleData: mockVehicleData,
            },
        })

        const mockFormData = {
            mode: 'create',
            data: {
                client_id: '1',
                brand: 'Toyota',
                model: 'Corolla',
                year: 2020,
                plate: 'ABC1234',
                vehicle_type: 'car',
            },
        }

        await (wrapper.vm as any).save(mockFormData)

        expect(VehiclesApi.update).not.toHaveBeenCalled()
        expect(wrapper.emitted('updated')).toBeFalsy()
    })

    it('handles API error gracefully', async () => {
        const { toast } = await import('vue-sonner')
        const { VehiclesApi } = await import('@/api/Vehicles')
        vi.mocked(VehiclesApi.update).mockRejectedValue(new Error('API Error'))
        
        const wrapper = mount(Update, {
            props: {
                show: true,
                vehicleData: mockVehicleData,
            },
        })

        await flushPromises()

        const mockFormData = {
            mode: 'edit',
            data: {
                client_id: '1',
                brand: 'Toyota',
                model: 'Corolla',
                year: 2020,
                plate: 'ABC1234',
                vehicle_type: 'car',
            },
        }

        await (wrapper.vm as any).save(mockFormData)
        await flushPromises()

        expect(toast.error).toHaveBeenCalledWith(
            'Erro ao atualizar veículo',
            { position: 'top-right' }
        )

        expect(wrapper.emitted('updated')).toBeFalsy()
        expect(wrapper.emitted('update:show')).toBeFalsy()
    })

    it('handles optional technical info fields', async () => {
        const { VehiclesApi } = await import('@/api/Vehicles')
        vi.mocked(VehiclesApi.update).mockResolvedValue({ status: 'success', message: 'Success', data: { id: '1' } })
        
        const wrapper = mount(Update, {
            props: {
                show: true,
                vehicleData: mockVehicleData,
            },
        })

        await flushPromises()

        const mockFormData = {
            mode: 'edit',
            data: {
                client_id: '1',
                brand: 'Toyota',
                model: 'Corolla',
                year: 2020,
                plate: 'ABC1234',
                vehicle_type: 'car',
                cilinder_capacity: '',
                vin: '',
                observations: '',
            },
        }

        await (wrapper.vm as any).save(mockFormData)
        await flushPromises()

        expect(VehiclesApi.update).toHaveBeenCalledWith(expect.objectContaining({
            cilinder_capacity: '',
            vin: '',
            observations: '',
        }))
    })
})
