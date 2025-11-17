<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Check, ChevronsUpDown } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import Button from '@/components/ui/button/Button.vue'
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import { ClientOption } from '../types'

interface Props {
    modelValue?: string
    placeholder?: string
    emptyText?: string
    searchPlaceholder?: string
    disabled?: boolean
    isLoading?: boolean
    clients?: ClientOption[]
    minSearchLength?: number
}

interface Emits {
    (e: 'update:modelValue', value: string): void
    (e: 'search', query: string): void
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Selecione um cliente...',
    emptyText: 'Nenhum cliente encontrado.',
    searchPlaceholder: 'Buscar cliente...',
    disabled: false,
    isLoading: false,
    clients: () => [],
    minSearchLength: 3
})

const emit = defineEmits<Emits>()

const open = ref(false)
const searchQuery = ref('')

const selectedClient = computed(() => {
    return props.clients?.find((client) => client.id === props.modelValue)
})

const displayValue = computed(() => {
    return selectedClient.value?.name || props.placeholder
})

const filteredClients = computed(() => {
    return props.clients || []
})

function onSelect(clientId: string) {
    emit('update:modelValue', clientId)
    open.value = false
}

function onClear() {
    emit('update:modelValue', '')
    searchQuery.value = ''
}

watch(searchQuery, (newQuery) => {
    emit('search', newQuery)
})

watch(open, (isOpen) => {
    if (!isOpen && !props.modelValue) {
        searchQuery.value = ''
    }
})
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button variant="outline" role="combobox" :aria-expanded="open" :disabled="disabled"
                class="w-full justify-between">
                {{ displayValue }}
                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-full p-0" align="start">
            <Command>
                <CommandInput v-model="searchQuery" :placeholder="searchPlaceholder" />
                <CommandList>
                    <!-- Mensagem quando está carregando -->
                    <CommandEmpty v-if="isLoading && filteredClients.length === 0">
                        <div class="flex items-center justify-center py-4">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                            <span class="ml-2">Buscando clientes...</span>
                        </div>
                    </CommandEmpty>

                    <!-- Mensagem quando precisa de mais caracteres E não há clientes -->
                    <CommandEmpty
                        v-else-if="searchQuery.length > 0 && searchQuery.length < minSearchLength && filteredClients.length === 0">
                        Digite pelo menos {{ minSearchLength }} caracteres para buscar
                    </CommandEmpty>

                    <!-- Mensagem quando não há resultados após busca válida -->
                    <CommandEmpty
                        v-else-if="searchQuery.length >= minSearchLength && !isLoading && filteredClients.length === 0">
                        {{ emptyText }}
                    </CommandEmpty>

                    <!-- Mensagem inicial quando não digitou nada E não há clientes -->
                    <CommandEmpty v-else-if="searchQuery.length === 0 && filteredClients.length === 0">
                        Digite para buscar clientes...
                    </CommandEmpty>

                    <!-- Lista de clientes - implementação customizada que funciona -->
                    <div v-if="filteredClients.length > 0" class="py-1">
                        <!-- Opção para limpar seleção -->
                        <div v-if="modelValue" @click="onClear"
                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground text-muted-foreground">
                            <span>Limpar seleção</span>
                        </div>

                        <!-- Lista de clientes -->
                        <div v-for="client in filteredClients" :key="client.id" @click="() => onSelect(client.id)"
                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                            <Check :class="cn(
                                'mr-2 h-4 w-4',
                                modelValue === client.id ? 'opacity-100' : 'opacity-0'
                            )" />
                            <div class="flex flex-col">
                                <span>{{ client.name }}</span>
                                <span v-if="client.document" class="text-sm text-muted-foreground">{{ client.document
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>