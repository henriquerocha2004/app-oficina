<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import { ProductsApi } from '@/api/Products';

interface ProductOption {
  id: string;
  name: string;
  sku: string;
}

interface Props {
  modelValue?: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:modelValue': [value: string | undefined];
}>();

const open = ref(false);
const products = ref<ProductOption[]>([]);
const isLoading = ref(false);

const selectedProduct = computed(() => {
  return products.value.find((p) => p.id === props.modelValue);
});

const displayValue = computed(() => {
  if (!selectedProduct.value) return 'Selecione um produto...';
  return `${selectedProduct.value.name} (${selectedProduct.value.sku})`;
});

onMounted(async () => {
  isLoading.value = true;
  try {
    const response = await ProductsApi.getActiveProducts();
    products.value = response.products;
  } catch (error) {
    console.error('Error loading products:', error);
  } finally {
    isLoading.value = false;
  }
});

function selectProduct(productId: string) {
  emit('update:modelValue', productId === props.modelValue ? undefined : productId);
  open.value = false;
}
</script>

<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        role="combobox"
        :aria-expanded="open"
        class="w-full justify-between"
      >
        {{ displayValue }}
        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-full p-0">
      <Command>
        <CommandInput placeholder="Buscar produto..." />
        <CommandEmpty>{{ isLoading ? 'Carregando...' : 'Nenhum produto encontrado.' }}</CommandEmpty>
        <CommandList>
          <CommandGroup>
            <CommandItem
              v-for="product in products"
              :key="product.id"
              :value="product.id"
              @select="selectProduct(product.id)"
            >
              <Check
                :class="cn(
                  'mr-2 h-4 w-4',
                  modelValue === product.id ? 'opacity-100' : 'opacity-0'
                )"
              />
              {{ product.name }} ({{ product.sku }})
            </CommandItem>
          </CommandGroup>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
