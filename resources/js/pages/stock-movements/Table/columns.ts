import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';
import type { StockMovementDetailInterface } from '../types';

const reasonLabels: Record<string, string> = {
    purchase: 'Compra',
    sale: 'Venda',
    adjustment: 'Ajuste',
    loss: 'Perda/Quebra',
    return: 'Devolução',
    transfer: 'Transferência',
    initial: 'Estoque Inicial',
    other: 'Outro',
};

export const columns: ColumnDef<StockMovementDetailInterface>[] = [
    {
        accessorKey: 'created_at',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Data', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]
            );
        },
        cell: ({ row }) => {
            const date = new Date(row.getValue('created_at'));
            return date.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
    },
    {
        accessorKey: 'product_name',
        header: 'Produto',
        cell: ({ row }) => {
            const productName = row.original.product_name;
            const productSku = row.original.product_sku;
            
            if (!productName) {
                return '-';
            }
            
            return h('div', { class: 'space-y-1' }, [
                h('p', { class: 'font-medium' }, productName),
                productSku ? h('p', { class: 'text-sm text-muted-foreground' }, `SKU: ${productSku}`) : null,
            ]);
        },
    },
    {
        accessorKey: 'movement_type',
        header: 'Tipo',
        cell: ({ row }) => {
            const type = row.getValue('movement_type') as string;
            return h(
                Badge,
                {
                    variant: type === 'in' ? 'default' : 'destructive',
                },
                () => (type === 'in' ? 'Entrada' : 'Saída')
            );
        },
    },
    {
        accessorKey: 'quantity',
        header: 'Quantidade',
        cell: ({ row }) => {
            const type = row.original.movement_type;
            const quantity = row.getValue('quantity') as number;
            return h(
                'span',
                {
                    class: type === 'in' ? 'text-green-600 font-medium' : 'text-red-600 font-medium',
                },
                type === 'in' ? `+${quantity}` : `-${quantity}`
            );
        },
    },
    {
        accessorKey: 'balance_after',
        header: 'Saldo Após',
        cell: ({ row }) => {
            return h('span', { class: 'font-medium' }, row.getValue('balance_after'));
        },
    },
    {
        accessorKey: 'reason',
        header: 'Motivo',
        cell: ({ row }) => {
            const reason = row.getValue('reason') as string;
            return reasonLabels[reason] || reason;
        },
    },
    {
        accessorKey: 'user_name',
        header: 'Usuário',
        cell: ({ row }) => {
            return row.original.user_name || '-';
        },
    },
];
