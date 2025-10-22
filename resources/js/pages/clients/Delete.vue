<script setup lang="ts">
import { Button } from "@/components/ui/button"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogClose,
} from "@/components/ui/dialog"
import { ClientInterface } from "./types";
import { ClientsApi } from "@/api/Clients";
import { toast } from "vue-sonner";

export interface DeleteClientProps {
    show: boolean
    client?: ClientInterface | null
}

const props = defineProps<DeleteClientProps>()

const emit = defineEmits(['deleted', 'update:show'])

async function confirmDelete() {
    const id = props.client?.id;
    if (!id) {
        toast.error('ID do cliente não encontrado', { position: 'top-right' });
        return;
    }

    const response = await ClientsApi.remove(id);
    if (response.status === 'error') {
        toast.error('Erro ao deletar cliente', { position: 'top-right' });
        return;
    }

    toast.success('Cliente deletado com sucesso', { position: 'top-right' });
    emit('deleted', id);
    emit('update:show', false);
}

</script>
<template>
    <div>
        <Dialog :open="props.show" @update:open="(value) => $emit('update:show', value)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Deletar Cliente</DialogTitle>
                    <DialogDescription>
                        Tem certeza que deseja deletar {{ props.client?.name }}?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose asChild>
                        <Button variant="outline">Cancelar</Button>
                    </DialogClose>
                    <Button variant="destructive" @click="confirmDelete">Deletar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>