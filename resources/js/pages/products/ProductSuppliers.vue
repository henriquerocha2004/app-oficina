<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import { ProductsApi } from '@/api/Products';
import { SuppliersApi } from '@/api/Suppliers';
import { toast } from 'vue-sonner';
import { Money3Component } from 'v-money3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Pencil, Trash2, Plus, Star } from 'lucide-vue-next';

interface Props {
    productId: string;
}

interface ProductSupplier {
    id: string;
    name: string;
    document_number: string;
    supplier_sku?: string;
    cost_price: number;
    lead_time_days?: number;
    min_order_quantity: number;
    is_preferred: boolean;
    notes?: string;
}

interface Supplier {
    id: string;
    name: string;
    document_number?: string;
}

const props = defineProps<Props>();

const suppliers = ref<ProductSupplier[]>([]);
const availableSuppliers = ref<Supplier[]>([]);
const isLoading = ref(false);
const showDialog = ref(false);
const isEditing = ref(false);

const formData = ref({
    supplier_id: '',
    supplier_sku: '',
    cost_price: 0,
    lead_time_days: undefined as number | undefined,
    min_order_quantity: 1,
    is_preferred: false,
    notes: '',
});

const currentSupplierId = ref<string>('');

// Configuração para formatação monetária brasileira
const moneyConfig = {
    decimal: ',',
    thousands: '.',
    prefix: 'R$ ',
    precision: 2,
    masked: false,
};

// Computed for switch to ensure proper binding
const isPreferredModel = computed({
    get: () => formData.value.is_preferred,
    set: (val: boolean) => { formData.value.is_preferred = val; }
});

onMounted(() => {
    loadSuppliers();
    loadAvailableSuppliers();
});

watch(() => props.productId, () => {
    if (props.productId) {
        loadSuppliers();
    }
});

async function loadSuppliers() {
    if (!props.productId) return;
    
    isLoading.value = true;
    try {
        const response = await ProductsApi.getSuppliers(props.productId);
        suppliers.value = response.suppliers;
    } catch (error) {
        console.error('Erro ao carregar fornecedores:', error);
        toast.error('Erro ao carregar fornecedores');
    } finally {
        isLoading.value = false;
    }
}

async function loadAvailableSuppliers() {
    try {
        const response = await SuppliersApi.search({ per_page: 1000 });
        availableSuppliers.value = response.suppliers.items.map(s => ({
            id: s.id!,
            name: s.name,
            document_number: s.document_number,
        }));
    } catch (error) {
        console.error('Erro ao carregar lista de fornecedores:', error);
    }
}

function openAddDialog() {
    isEditing.value = false;
    resetForm();
    showDialog.value = true;
}

function openEditDialog(supplier: ProductSupplier) {
    isEditing.value = true;
    currentSupplierId.value = supplier.id;
    formData.value = {
        supplier_id: supplier.id,
        supplier_sku: supplier.supplier_sku || '',
        cost_price: supplier.cost_price,
        lead_time_days: supplier.lead_time_days,
        min_order_quantity: supplier.min_order_quantity,
        is_preferred: supplier.is_preferred,
        notes: supplier.notes || '',
    };
    showDialog.value = true;
}

function resetForm() {
    formData.value = {
        supplier_id: '',
        supplier_sku: '',
        cost_price: 0,
        lead_time_days: undefined,
        min_order_quantity: 1,
        is_preferred: false,
        notes: '',
    };
    currentSupplierId.value = '';
}

async function handleSubmit() {
    if (!isEditing.value && !formData.value.supplier_id) {
        toast.error('Selecione um fornecedor');
        return;
    }

    if (formData.value.cost_price <= 0) {
        toast.error('Preço de custo deve ser maior que zero');
        return;
    }

    isLoading.value = true;
    try {
        if (isEditing.value) {
            await ProductsApi.updateSupplier(props.productId, currentSupplierId.value, {
                supplier_sku: formData.value.supplier_sku || undefined,
                cost_price: formData.value.cost_price,
                lead_time_days: formData.value.lead_time_days,
                min_order_quantity: formData.value.min_order_quantity,
                is_preferred: formData.value.is_preferred,
                notes: formData.value.notes || undefined,
            });
            toast.success('Fornecedor atualizado com sucesso');
        } else {
            await ProductsApi.attachSupplier(props.productId, {
                supplier_id: formData.value.supplier_id,
                supplier_sku: formData.value.supplier_sku || undefined,
                cost_price: formData.value.cost_price,
                lead_time_days: formData.value.lead_time_days,
                min_order_quantity: formData.value.min_order_quantity,
                is_preferred: formData.value.is_preferred,
                notes: formData.value.notes || undefined,
            });
            toast.success('Fornecedor vinculado com sucesso');
        }
        
        showDialog.value = false;
        resetForm();
        await loadSuppliers();
    } catch (error: any) {
        console.error('Erro ao salvar fornecedor:', error);
        toast.error(error.message || 'Erro ao salvar fornecedor');
    } finally {
        isLoading.value = false;
    }
}

async function handleRemove(supplierId: string) {
    if (!confirm('Tem certeza que deseja remover este fornecedor?')) {
        return;
    }

    isLoading.value = true;
    try {
        await ProductsApi.detachSupplier(props.productId, supplierId);
        toast.success('Fornecedor removido com sucesso');
        await loadSuppliers();
    } catch (error) {
        console.error('Erro ao remover fornecedor:', error);
        toast.error('Erro ao remover fornecedor');
    } finally {
        isLoading.value = false;
    }
}

function formatCurrency(value: number) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <p class="text-sm text-muted-foreground">
                Gerencie os fornecedores deste produto
            </p>
            <Button @click="openAddDialog" size="sm" type="button">
                <Plus class="h-4 w-4 mr-2" />
                Adicionar Fornecedor
            </Button>
        </div>

        <div v-if="isLoading && suppliers.length === 0" class="text-center py-8 text-muted-foreground">
            Carregando...
        </div>

        <div v-else-if="suppliers.length === 0" class="text-center py-8 text-muted-foreground">
            Nenhum fornecedor vinculado
        </div>

        <div v-else class="border rounded-lg">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Fornecedor</TableHead>
                        <TableHead>SKU Fornecedor</TableHead>
                        <TableHead>Preço de Custo</TableHead>
                        <TableHead>Prazo (dias)</TableHead>
                        <TableHead>Qtd. Mín.</TableHead>
                        <TableHead>Preferencial</TableHead>
                        <TableHead class="text-right">Ações</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="supplier in suppliers" :key="supplier.id">
                        <TableCell>
                            <div>
                                <div class="font-medium">{{ supplier.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ supplier.document_number }}</div>
                            </div>
                        </TableCell>
                        <TableCell>{{ supplier.supplier_sku || '-' }}</TableCell>
                        <TableCell>{{ formatCurrency(supplier.cost_price) }}</TableCell>
                        <TableCell>{{ supplier.lead_time_days || '-' }}</TableCell>
                        <TableCell>{{ supplier.min_order_quantity }}</TableCell>
                        <TableCell>
                            <Star v-if="supplier.is_preferred" class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                            <span v-else class="text-muted-foreground">-</span>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex gap-2 justify-end">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    type="button"
                                    @click="openEditDialog(supplier)"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    type="button"
                                    @click="handleRemove(supplier.id)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <Dialog :open="showDialog" @update:open="(val) => { showDialog = val; if (!val) resetForm(); }">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Editar Fornecedor' : 'Adicionar Fornecedor' }}</DialogTitle>
                </DialogHeader>

                <div class="space-y-4 py-4">
                    <div v-if="!isEditing" class="space-y-2">
                        <Label>Fornecedor *</Label>
                        <Select v-model="formData.supplier_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione um fornecedor" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="supplier in availableSuppliers"
                                    :key="supplier.id"
                                    :value="supplier.id"
                                >
                                    {{ supplier.name }} ({{ supplier.document_number }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>SKU do Fornecedor</Label>
                            <Input v-model="formData.supplier_sku" placeholder="Código do produto no fornecedor" />
                        </div>

                        <div class="space-y-2">
                            <Label>Preço de Custo *</Label>
                            <Money3Component
                                v-model.number="formData.cost_price"
                                v-bind="moneyConfig"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Prazo de Entrega (dias)</Label>
                            <Input
                                v-model.number="formData.lead_time_days"
                                type="number"
                                min="0"
                                placeholder="Dias para entrega"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Quantidade Mínima *</Label>
                            <Input
                                v-model.number="formData.min_order_quantity"
                                type="number"
                                min="1"
                            />
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Switch
                            id="is_preferred"
                            v-model="isPreferredModel"
                        />
                        <Label for="is_preferred" class="cursor-pointer">Fornecedor preferencial</Label>
                    </div>

                    <div class="space-y-2">
                        <Label>Observações</Label>
                        <Textarea
                            v-model="formData.notes"
                            placeholder="Informações adicionais sobre este fornecedor"
                            rows="3"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" type="button" @click="showDialog = false">Cancelar</Button>
                    <Button type="button" @click="handleSubmit" :disabled="isLoading">
                        {{ isEditing ? 'Atualizar' : 'Adicionar' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
