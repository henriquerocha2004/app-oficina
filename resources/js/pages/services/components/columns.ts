import { Service } from '@/types';
import { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import ServiceActions from './ServiceActions.vue';

export const columns: ColumnDef<Service>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
    },
    {
        accessorKey: 'category',
        header: 'Categoria',
        cell: ({ row }) => {
            const categoryLabels: Record<string, string> = {
                maintenance: 'Manutenção',
                repair: 'Reparo',
                diagnostic: 'Diagnóstico',
                painting: 'Pintura',
                alignment: 'Alinhamento',
                other: 'Outro',
            };
            return categoryLabels[row.original.category] || row.original.category;
        },
    },
    {
        accessorKey: 'base_price',
        header: 'Preço Base',
        cell: ({ row }) => {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            }).format(row.original.base_price);
        },
    },
    {
        accessorKey: 'estimated_time',
        header: 'Tempo Estimado',
        cell: ({ row }) => {
            if (!row.original.estimated_time) return '-';
            return `${row.original.estimated_time} min`;
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const isActive = row.original.is_active;
            return h('span', {
                class: isActive 
                    ? 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                    : 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
            }, isActive ? 'Ativo' : 'Inativo');
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => h(ServiceActions, { service: row.original }),
    },
];
