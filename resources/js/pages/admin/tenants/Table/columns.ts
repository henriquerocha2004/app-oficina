import Button from '@/components/ui/button/Button.vue';
import { ColumnDef } from '@tanstack/vue-table';
import { ArrowUpDown, CheckCircle, XCircle } from 'lucide-vue-next';
import { h } from 'vue';
import type { Tenant } from '../types';
import Actions from './ActionButtons.vue';

export const columns: ColumnDef<Tenant>[] = [
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
        accessorKey: 'slug',
        header: 'Slug',
    },
    {
        accessorKey: 'subscription_plan',
        header: 'Plano',
        cell: ({ row }: { row: { original: Tenant } }) => {
            return row.original.subscription_plan?.name || '-';
        },
    },
    {
        accessorKey: 'domains',
        header: 'Domínio',
        cell: ({ row }: { row: { original: Tenant } }) => {
            return row.original.domains?.[0]?.domain || '-';
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }: { row: { original: Tenant } }) => {
            return row.original.is_active
                ? h('div', { class: 'flex items-center gap-2 text-green-600' }, [
                      h(CheckCircle, { class: 'h-4 w-4' }),
                      'Ativa',
                  ])
                : h('div', { class: 'flex items-center gap-2 text-red-600' }, [
                      h(XCircle, { class: 'h-4 w-4' }),
                      'Inativa',
                  ]);
        },
    },
    {
        accessorKey: 'id',
        header: 'Ações',
        cell: ({ row }: { row: { original: Tenant } }) => {
            return h(Actions, { tenant: row.original });
        },
    },
];
