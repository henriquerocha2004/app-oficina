<script setup lang="ts">
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"

export interface DeleteClientProps {
  show: boolean
  clientId?: string | null
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
                    <Button variant="outline">Cancelar</Button>
                    <Button variant="destructive">Deletar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>