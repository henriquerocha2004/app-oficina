import Button from '@/components/ui/button/Button.vue';
import { ColumnDef } from '@tanstack/vue-table';
import { cnpj, cpf } from 'cpf-cnpj-validator';
import { ArrowUpDown } from 'lucide-vue-next';
import { h } from 'vue';
import { VehiclesInterface } from '../types';
import Actions from '@/pages/vehicles/Table/ActionButtons.vue';
import formatPhone from '@/utils/formatPhone';
import VehicleCell from '@/pages/vehicles/Table/Cells/VehicleCell.vue';
import PlateCell from './Cells/PlateCell.vue';
import ClientCell from './Cells/ClientCell.vue';

export const columns: ColumnDef<VehiclesInterface>[] = [
    {
        accessorKey: 'model',
        header: ({ column }: { column: any }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Veiculo', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }: { row: { original: VehiclesInterface } }) => {
            return h(VehicleCell, { vehicle: row.original });
        }
    },
    {
        accessorKey: 'plate',
        header: 'Placa',
        cell: ({ row }: { row: { original: VehiclesInterface } }) => {
            return h(PlateCell, { vehicle: row.original });
        },
    },
    {
        accessorKey: 'client',
        header: 'Cliente',
        cell: ({ row }: { row: { original: VehiclesInterface } }) => {
            return h(ClientCell, { vehicle: row.original });
        }
    },
    {
        accessorKey: 'last_service_date',
        header: 'Último Serviço',
        cell: ({ row }: { row: { original: VehiclesInterface } }) => {
           return '--'
        },
    },
    {
        accessorKey: 'status',
        header: 'Status',
    },
    {
        accessorKey: 'id',
        header: 'Ações',
        cell: ({ row }: { row: { original: VehiclesInterface } }) => {
            return h(Actions, { vehicle: row.original });
        },
    },
];
