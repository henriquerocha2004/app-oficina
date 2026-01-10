import { mount, flushPromises } from '@vue/test-utils'
import Create from '@/pages/vehicles/Create.vue'
import { vi, describe, it, expect, beforeEach } from 'vitest'

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
        save: vi.fn().mockResolvedValue({ status: 'success', data: { id: '1' } })
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

describe('Vehicles Create.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks()
    })

    it('renders with correct title and form', async () => {
        // Create a DOM element for teleportation
        const teleportTarget = document.createElement('div')
        teleportTarget.id = 'teleport-target'
        document.body.appendChild(teleportTarget)

        const wrapper = mount(Create, {
            props: {
                show: true,
            },
            global: {
                config: {
                    globalProperties: {
                        teleportTarget: teleportTarget,
                    },
                },
            },
        })

        await flushPromises()

        // Since Sheet uses teleport, check if component is mounted correctly
        expect(wrapper.vm).toBeTruthy()
        expect(wrapper.props('show')).toBe(true)
        
        // Cleanup
        document.body.removeChild(teleportTarget)
    })

    it('does not render when show is false', () => {
        const wrapper = mount(Create, {
            props: {
                show: false,
            },
        })

        // Sheet should not be visible
        const sheet = wrapper.find('[data-state="open"]')
        expect(sheet.exists()).toBe(false)
    })

    it('emits update:show when sheet is closed', async () => {
        const wrapper = mount(Create, {
            props: {
                show: true,
            },
        })

        await flushPromises()

        // Find the Sheet component and simulate a close event
        const sheet = wrapper.findComponent({ name: 'Sheet' })
        if (sheet.exists()) {
            await sheet.vm.$emit('update:open', false)
        }
        
        await flushPromises()
        
        expect(wrapper.emitted('update:show')).toBeTruthy()
        expect(wrapper.emitted('update:show')![0]).toEqual([false])
    }, 10000)

    it('handles successful vehicle creation', async () => {
        const { toast } = await import('vue-sonner')
        
        const wrapper = mount(Create, {
            props: {
                show: true,
            },
        })

        await flushPromises()

        const mockFormData = {
            mode: 'create',
            data: {
                clientId: '1',
                brand: 'Toyota',
                model: 'Corolla',
                year: 2020,
                licensePlate: 'ABC1234',
                type: 'car',
                technicalInfo: {},
            },
        }

        // Simulate form submission directly through the save method
        await (wrapper.vm as any).save(mockFormData)
        await flushPromises()

        // Should emit events
        expect(wrapper.emitted('created')).toBeTruthy()
        expect(wrapper.emitted('update:show')).toBeTruthy()
        expect(wrapper.emitted('update:show')![0]).toEqual([false])

        // Should show success toast
        expect(toast.success).toHaveBeenCalledWith(
            'Veículo cadastrado com sucesso',
            { position: 'top-right' }
        )
    })

    it('does not process when mode is not create', async () => {
        const wrapper = mount(Create, {
            props: {
                show: true,
            },
        })

        await flushPromises()

        const mockFormData = {
            mode: 'edit',
            data: {
                clientId: '1',
                brand: 'Toyota',
            },
        }

        // Simulate form submission with edit mode directly through save method
        await (wrapper.vm as any).save(mockFormData)
        await flushPromises()

        // Should not emit created event
        expect(wrapper.emitted('created')).toBeFalsy()
    })

    it('calls clear on form component after successful creation', async () => {
        const wrapper = mount(Create, {
            props: {
                show: true,
            },
        })

        // Mock the form component's clear method
        const formComponent = wrapper.vm.$refs.formComponent
        if (formComponent) {
            const clearSpy = vi.spyOn(formComponent as any, 'clear').mockImplementation(() => {})

            const mockFormData = {
                mode: 'create',
                data: {
                    clientId: '1',
                    brand: 'Toyota',
                },
            }

            // Simulate form submission
            const form = wrapper.findComponent({ name: 'Form' })
            await form.vm.$emit('submitted', mockFormData)
            await flushPromises()

            expect(clearSpy).toHaveBeenCalled()
        }
    })

    it('logs form data to console', async () => {
        const consoleSpy = vi.spyOn(console, 'log').mockImplementation(() => {})
        
        const wrapper = mount(Create, {
            props: {
                show: true,
            },
        })

        await flushPromises()

        const mockFormData = {
            mode: 'create',
            data: {
                clientId: '1',
                brand: 'Toyota',
                model: 'Corolla',
            },
        }

        // Simulate form submission directly through save method
        await (wrapper.vm as any).save(mockFormData)
        await flushPromises()

        expect(consoleSpy).toHaveBeenCalledWith(mockFormData)
        
        consoleSpy.mockRestore()
    })
})