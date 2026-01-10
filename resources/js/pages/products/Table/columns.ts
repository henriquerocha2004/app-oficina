import type { ColumnDef } from '@tanstack/vue-table';
import type { ProductInterface } from '../types';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';

const categoryLabels: Record<string, string> = {
    engine: 'Motor',
    suspension: 'Suspensão',
    brakes: 'Freios',
    electrical: 'Elétrica',
    filters: 'Filtros',
    fluids: 'Fluidos',
    tires: 'Pneus',
    body_parts: 'Lataria',
    interior: 'Interior',
    tools: 'Ferramentas',
    other: 'Outros',
};

const unitLabels: Record<string, string> = {
    unit: 'un',
    liter: 'L',
    kg: 'kg',
    meter: 'm',
    box: 'cx',
};

export const columns: ColumnDef<ProductInterface>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Nome', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]
            );
        },
    },
    {
        accessorKey: 'sku',
        header: 'SKU',
        cell: ({ row }) => row.original.sku || '-',
    },
    {
        accessorKey: 'manufacturer',
        header: 'Fabricante',
        cell: ({ row }) => row.original.manufacturer || '-',
    },
    {
        accessorKey: 'category',
        header: 'Categoria',
        cell: ({ row }) => categoryLabels[row.original.category] || row.original.category,
    },
    {
        accessorKey: 'stock_quantity',
        header: 'Estoque',
        cell: ({ row }) => {
            const product = row.original;
            const stock = product.stock_quantity;
            const minStock = product.min_stock_level || 0;
            const unit = unitLabels[product.unit] || product.unit;

            let variant: 'default' | 'destructive' | 'warning' = 'default';
            
            if (minStock > 0) {
                if (stock <= minStock) {
                    variant = 'destructive';
                } else if (stock <= minStock * 1.5) {
                    variant = 'warning' as any;
                }
            }

            return h(Badge, { variant }, () => `${stock} ${unit}`);
        },
    },
    {
        accessorKey: 'unit_price',
        header: 'Preço',
        cell: ({ row }) => {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            }).format(row.original.unit_price);
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const isActive = row.original.is_active;
            return h(
                Badge,
                { variant: isActive ? 'default' : 'secondary' },
                () => isActive ? 'Ativo' : 'Inativo'
            );
        },
    },
];
