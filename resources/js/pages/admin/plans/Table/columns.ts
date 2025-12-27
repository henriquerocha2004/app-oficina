import Button from '@/components/ui/button/Button.vue';
import { ColumnDef } from '@tanstack/vue-table';
import { ArrowUpDown, CheckCircle, XCircle } from 'lucide-vue-next';
import { h } from 'vue';
import type { SubscriptionPlan } from '../types';
import Actions from './ActionButtons.vue';

export const columns: ColumnDef<SubscriptionPlan>[] = [
    {
        accessorKey: 'name',
        header: ({ column }: { column: any }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Nome', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
    },
    {
        accessorKey: 'price',
        header: 'Preço',
        cell: ({ row }: { row: { original: SubscriptionPlan } }) => {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            }).format(row.original.price);
        },
    },
    {
        accessorKey: 'billing_cycle',
        header: 'Ciclo',
        cell: ({ row }: { row: { original: SubscriptionPlan } }) => {
            return row.original.billing_cycle === 'monthly' ? 'Mensal' : 'Anual';
        },
    },
    {
        accessorKey: 'max_users',
        header: 'Usuários',
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }: { row: { original: SubscriptionPlan } }) => {
            return row.original.is_active
                ? h('div', { class: 'flex items-center gap-2 text-green-600' }, [
                      h(CheckCircle, { class: 'h-4 w-4' }),
                      'Ativo',
                  ])
                : h('div', { class: 'flex items-center gap-2 text-red-600' }, [
                      h(XCircle, { class: 'h-4 w-4' }),
                      'Inativo',
                  ]);
        },
    },
    {
        accessorKey: 'id',
        header: 'Ações',
        cell: ({ row }: { row: { original: SubscriptionPlan } }) => {
            return h(Actions, { plan: row.original });
        },
    },
];
