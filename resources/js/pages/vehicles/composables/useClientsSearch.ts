import { ref, computed } from 'vue'
import { ClientOption } from '../types'
import { ClientsApi } from '@/api/Clients'
import type { SearchParams } from '@/pages/Shared/Search/SearchParams'

export function useClientsSearch() {
  const clients = ref<ClientOption[]>([])
  const isLoading = ref(false)
  const searchQuery = ref('')
  const minSearchLength = 3
  let debounceTimeout: ReturnType<typeof setTimeout> | null = null

  const loadClients = async (query: string) => {
    if (query.length < minSearchLength) {
      clients.value = []
      return
    }

    isLoading.value = true
    
    try {
      const params: SearchParams = {
        per_page: 20,
        page: 1,
        search: query,
        sort_direction: 'asc',
        sort_by: 'name',
      }

      const response = await ClientsApi.search(params)
      
      if (!response.clients || !response.clients.items) {
        console.error('Estrutura de resposta inesperada:', response)
        clients.value = []
        return
      }
      
      clients.value = response.clients.items.map((client) => ({
        id: client?.id?.toString() ?? '',
        name: client.name,
        document_number: client.document_number,
      }))

    } catch (error) {
      console.error('Erro ao buscar clientes:', error)
      clients.value = []
    } finally {
      isLoading.value = false
    }
  }

  const searchClients = (query: string) => {
    searchQuery.value = query

    if (debounceTimeout) {
      clearTimeout(debounceTimeout)
    }

    if (query.length < minSearchLength) {
      clients.value = []
      return
    }

    debounceTimeout = setTimeout(() => {
      loadClients(query)
    }, 500)
  }

  const getClientById = (id: string): ClientOption | undefined => {
    return clients.value.find(client => client.id === id)
  }

  const clearSearch = () => {
    searchQuery.value = ''
    clients.value = []
    if (debounceTimeout) {
      clearTimeout(debounceTimeout)
      debounceTimeout = null
    }
  }

  const setClient = (client: ClientOption) => {
    clients.value = [client]
  }

  return {
    clients: computed(() => clients.value),
    isLoading: computed(() => isLoading.value),
    searchQuery: computed(() => searchQuery.value),
    searchClients,
    getClientById,
    clearSearch,
    setClient,
    minSearchLength,
  }
}