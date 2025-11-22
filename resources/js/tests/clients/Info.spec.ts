import { mount } from '@vue/test-utils'
import Info from '@/pages/clients/Info.vue'
import { describe, it, expect, vi } from 'vitest'
// no-op

vi.mock('@/utils/formatPhone', () => ({
    default: (v: string) => `phone(${v})`,
}))

vi.mock('@/utils/formatDocument', () => ({
    default: (v: string) => `doc(${v})`,
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
})
