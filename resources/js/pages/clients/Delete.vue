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
import { ClientInterface } from "./client";

export interface DeleteClientProps {
    show: boolean
    client?: ClientInterface | null
}

const props = defineProps<DeleteClientProps>()

</script>
<template>
    <div>
        <Dialog :open="props.show" @update:open="(value) => $emit('update:show', value)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Deletar Cliente</DialogTitle>
                    <DialogDescription>
                        Tem certeza que deseja deletar este cliente?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose asChild>
                        <Button variant="outline">Cancelar</Button>
                    </DialogClose>
                    <Button variant="destructive">Deletar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>