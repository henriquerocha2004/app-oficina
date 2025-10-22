import Button from '@/components/ui/button/Button.vue';
import { ColumnDef } from '@tanstack/vue-table';
import { cnpj, cpf } from 'cpf-cnpj-validator';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { ClientInterface } from '../types';
import Actions from '@/pages/clients/Table/ActionButtons.vue';
import formatPhone from '@/utils/formatPhone';

export const columns: ColumnDef<ClientInterface>[] = [
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
        accessorKey: 'email',
        header: 'Email',
    },
    {
        accessorKey: 'document',
        header: 'Documento',
        cell: ({ row }: { row: { original: ClientInterface } }) => {
            if (row.original.document.length === 11) {
                return cpf.format(row.original.document);
            }

            if (row.original.document.length === 14) {
                return cnpj.format(row.original.document);
            }

            return row.original.document;
        },
    },
    {
        accessorKey: 'phone',
        header: 'Telefone',
        cell: ({ row }: { row: { original: ClientInterface } }) => {
            return formatPhone(row.original.phone.number);
        },
    },
    {
        accessorKey: 'id',
        header: 'Ações',
        cell: ({ row }: { row: { original: ClientInterface } }) => {
            return h(Actions, { client: row.original });
        },
    },
];
