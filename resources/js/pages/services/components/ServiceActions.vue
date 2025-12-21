<script setup lang="ts">
import { Service } from '@/types';
import { inject } from 'vue';
import { Button } from '@/components/ui/button';
import { MoreHorizontal, Pencil, Eye, Trash2 } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const props = defineProps<{
    service: Service;
}>();

const onEditService = inject<(service: Service) => void>('onEditService');
const onViewService = inject<(service: Service) => void>('onViewService');
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon">
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="onViewService?.(service)">
                <Eye class="mr-2 h-4 w-4" />
                Ver Detalhes
            </DropdownMenuItem>
            <DropdownMenuItem @click="onEditService?.(service)">
                <Pencil class="mr-2 h-4 w-4" />
                Editar
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
