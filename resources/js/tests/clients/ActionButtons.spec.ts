import { mount } from '@vue/test-utils'
import ActionButtons from '@/pages/clients/Table/ActionButtons.vue'
import { describe, it, expect, vi } from 'vitest'

describe('Clients ActionButtons.vue', () => {
    const client = {
        id: '01A', name: 'A', email: 'a@a.com', document: '123', phone: { number: '11' }, address: { street: '', city: '', state: '', zipCode: '' }, observations: ''
    }

    it('calls injected handlers on click', async () => {
        const onViewClient = vi.fn()
        const onEditClient = vi.fn()
        const onDeleteClient = vi.fn()

        const wrapper = mount(ActionButtons, {
            props: { client },
            global: {
                provide: { onViewClient, onEditClient, onDeleteClient },
                stubs: {
                    // Stub Button to a simple button for click
                    Button: {
                        template: '<button @click="$emit(\'click\')"><slot /></button>'
                    },
                    'lucide-vue-next': true,
                },
            },
        })

        const buttons = wrapper.findAll('button')
        await buttons[0].trigger('click')
        await buttons[1].trigger('click')
        await buttons[2].trigger('click')

        expect(onViewClient).toHaveBeenCalledWith(client)
        expect(onEditClient).toHaveBeenCalledWith(client)
        expect(onDeleteClient).toHaveBeenCalledWith(client)
    })
})
