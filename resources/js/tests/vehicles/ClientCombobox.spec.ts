import { mount } from '@vue/test-utils'
import ClientCombobox from '@/pages/vehicles/components/ClientCombobox.vue'
import { vi, describe, it, expect, beforeEach } from 'vitest'

// Mock simples dos componentes shadcn
vi.mock('@/lib/utils', () => ({
  cn: (...classes: any[]) => classes.filter(Boolean).join(' ')
}))

vi.mock('lucide-vue-next', () => ({
  Check: { template: '<span data-testid="check-icon">✓</span>' },
  ChevronsUpDown: { template: '<span data-testid="chevrons-icon">↕</span>' }
}))

vi.mock('@/components/ui/button/Button.vue', () => ({
  default: {
    name: 'Button',
    template: '<button data-testid="combobox-trigger" @click="$emit(\'click\')"><slot /></button>',
    emits: ['click']
  }
}))

vi.mock('@/components/ui/popover', () => ({
  Popover: { template: '<div><slot /></div>' },
  PopoverContent: { template: '<div data-testid="popover-content"><slot /></div>' },
  PopoverTrigger: { template: '<div><slot /></div>' }
}))

vi.mock('@/components/ui/command', () => ({
  Command: { template: '<div><slot /></div>' },
  CommandEmpty: { template: '<div data-testid="command-empty"><slot /></div>' },
  CommandGroup: { template: '<div><slot /></div>' },
  CommandInput: {
    template: '<input data-testid="search-input" @input="$emit(\'update:modelValue\', $event.target.value)" />',
    emits: ['update:modelValue']
  },
  CommandItem: {
    template: '<div data-testid="command-item" @click="$emit(\'select\', value)"><slot /></div>',
    props: ['value'],
    emits: ['select']
  },
  CommandList: { template: '<div><slot /></div>' }
}))

const mockClients = [
  { id: '1', name: 'João Silva', document: '123.456.789-00' },
  { id: '2', name: 'Maria Santos', document: '987.654.321-00' },
  { id: '3', name: 'Pedro Oliveira', document: '111.222.333-44' }
]

describe('ClientCombobox - Testes Completos', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renderiza corretamente com placeholder padrão', () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    expect(wrapper.exists()).toBe(true)
    
    // Verifica se o componente foi montado
    const component = wrapper.findComponent({ name: 'ClientCombobox' })
    expect(component.exists()).toBe(true)
  })

  it('exibe o nome do cliente selecionado quando modelValue é fornecido', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        modelValue: '2',
        clients: mockClients
      }
    })

    await wrapper.vm.$nextTick()

    // Verifica se o cliente correto foi selecionado internamente
    expect(wrapper.vm.selectedClient?.name).toBe('Maria Santos')
  })

  it('emite update:modelValue quando um cliente é selecionado', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    // Simula seleção de cliente através do método interno
    await wrapper.vm.onSelect('1')

    // Verifica se o evento foi emitido
    const emittedEvents = wrapper.emitted('update:modelValue')
    expect(emittedEvents).toBeTruthy()
    expect(emittedEvents![0]).toEqual(['1'])
  })

  it('limpa a seleção quando clear é chamado', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        modelValue: '1',
        clients: mockClients
      }
    })

    // Simula limpeza através do método interno
    await wrapper.vm.onClear()

    // Verifica se o evento de limpeza foi emitido
    const emittedEvents = wrapper.emitted('update:modelValue')
    expect(emittedEvents).toBeTruthy()
    expect(emittedEvents![0]).toEqual([''])
  })

  it('filteredClients sempre retorna todos os clientes (backend faz a filtragem)', () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    // Com a integração backend, filteredClients sempre retorna props.clients
    wrapper.vm.searchQuery = 'João'
    expect(wrapper.vm.filteredClients.length).toBe(3)
    expect(wrapper.vm.filteredClients).toEqual(mockClients)
  })

  it('emite evento de busca quando query muda', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    // Simula mudança na busca
    wrapper.vm.searchQuery = 'Maria'
    await wrapper.vm.$nextTick()

    // O watcher deveria ter emitido o evento de busca
    const emittedEvents = wrapper.emitted('search')
    expect(emittedEvents).toBeTruthy()
    expect(emittedEvents![0]).toEqual(['Maria'])
  })

  it('lida com lista vazia de clientes', () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: []
      }
    })

    expect(wrapper.vm.filteredClients.length).toBe(0)
    expect(() => wrapper.vm.onSelect('1')).not.toThrow()
  })

  it('controla o estado open do popover', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    // Estado inicial
    expect(wrapper.vm.open).toBe(false)

    // Abre o popover
    wrapper.vm.open = true
    await wrapper.vm.$nextTick()
    expect(wrapper.vm.open).toBe(true)

    // Fecha após seleção
    await wrapper.vm.onSelect('1')
    expect(wrapper.vm.open).toBe(false)
  })

  it('limpa query de busca apenas quando popover fecha sem seleção', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients,
        modelValue: '' // Sem seleção inicial
      }
    })

    // Define uma query de busca
    wrapper.vm.searchQuery = 'João'
    expect(wrapper.vm.searchQuery).toBe('João')

    // Abre o popover
    wrapper.vm.open = true
    await wrapper.vm.$nextTick()

    // Fecha o popover sem seleção - deve limpar a query
    wrapper.vm.open = false
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.searchQuery).toBe('')
  })

  it('aplica props customizadas corretamente', () => {
    const customProps = {
      modelValue: '1',
      placeholder: 'Placeholder customizado',
      emptyText: 'Texto vazio customizado',
      searchPlaceholder: 'Buscar customizado',
      disabled: true,
      clients: mockClients
    }

    const wrapper = mount(ClientCombobox, {
      props: customProps
    })

    // Verifica se as props foram aplicadas
    expect(wrapper.props()).toMatchObject(customProps)
  })

  it('emite evento de busca quando searchQuery muda', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    // Monitora o evento search
    wrapper.vm.searchQuery = 'pedro'
    await wrapper.vm.$nextTick()

    // Verifica se o evento foi emitido com o valor correto
    expect(wrapper.emitted('search')).toBeTruthy()
    expect(wrapper.emitted('search')?.[0]).toEqual(['pedro'])
  })

  it('limpa query quando popover fecha', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    wrapper.vm.searchQuery = 'João'
    wrapper.vm.open = true
    await wrapper.vm.$nextTick()

    wrapper.vm.open = false
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.searchQuery).toBe('')
  })

  it('retorna lista completa quando query está vazia', () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    wrapper.vm.searchQuery = ''
    expect(wrapper.vm.filteredClients).toEqual(mockClients)
  })

  it('verifica computed selectedClient funciona corretamente', () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        modelValue: '2',
        clients: mockClients
      }
    })

    expect(wrapper.vm.selectedClient?.id).toBe('2')
    expect(wrapper.vm.selectedClient?.name).toBe('Maria Santos')
  })

  it('funciona com cliente não existente no modelValue', () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        modelValue: '999',
        clients: mockClients
      }
    })

    expect(wrapper.vm.selectedClient).toBeUndefined()
  })

  it('testa comportamento dos watchers', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients
      }
    })

    // Testa watcher de searchQuery
    wrapper.vm.searchQuery = 'teste'
    await wrapper.vm.$nextTick()

    const searchEmitted = wrapper.emitted('search')
    expect(searchEmitted).toBeTruthy()

    // Testa watcher de open
    wrapper.vm.searchQuery = 'algum texto'
    wrapper.vm.open = true
    await wrapper.vm.$nextTick()

    wrapper.vm.open = false
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.searchQuery).toBe('')
  })

  it('preserva query de busca quando há seleção ativa', async () => {
    const wrapper = mount(ClientCombobox, {
      props: {
        clients: mockClients,
        modelValue: '1' // Cliente selecionado
      }
    })

    // Define uma query de busca
    wrapper.vm.searchQuery = 'João'
    expect(wrapper.vm.searchQuery).toBe('João')

    // Abre e fecha o popover
    wrapper.vm.open = true
    await wrapper.vm.$nextTick()
    
    wrapper.vm.open = false
    await wrapper.vm.$nextTick()

    // Query não deve ser limpa porque há seleção ativa
    expect(wrapper.vm.searchQuery).toBe('João')
  })
})