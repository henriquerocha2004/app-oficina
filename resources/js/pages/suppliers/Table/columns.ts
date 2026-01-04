import type { ColumnDef } from '@tanstack/vue-table';
import type { SupplierInterface } from '../types';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';
import { cnpj } from 'cpf-cnpj-validator';

export const columns: ColumnDef<SupplierInterface>[] = [
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
        accessorKey: 'document_number',
        header: 'CNPJ',
        cell: ({ row }) => {
            try {
                return cnpj.format(row.original.document_number);
            } catch {
                return row.original.document_number;
            }
        },
    },
    {
        accessorKey: 'contact',
        header: 'Contato',
        cell: ({ row }) => {
            return row.original.email || row.original.phone || '-';
        },
    },
    {
        accessorKey: 'city',
        header: 'Cidade/UF',
        cell: ({ row }) => {
            const city = row.original.city;
            const state = row.original.state;
            if (city && state) {
                return `${city}/${state}`;
            }
            return city || state || '-';
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
