<script setup lang="ts">
import {
  Drawer,
  DrawerContent,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
} from '@/components/ui/drawer';

import { Car } from 'lucide-vue-next';

export interface UpdateClientProps {
    show: boolean;
}

const props = defineProps<UpdateClientProps>();

</script>
<template>
    <div>
        <Drawer :open="props.show" @update:open="(value) => $emit('update:show', value)">
            <DrawerContent>
                <DrawerHeader>
                    <DrawerTitle class="text-xl">Detalhes do Cliente</DrawerTitle>
                </DrawerHeader>
                <div class="flex flex-row gap-4 p-4">
                    <div class="flex flex-col gap-4 border-r-2 w-1/3 pr-4">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">Informações Pessoais</h2>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Nome Completo</p>
                            <p class="font-bold">João Silva</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Telefone</p>
                            <p class="font-bold">(11) 98765-4321</p>
                        </div>
                         <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">CPF/CNPJ</p>
                            <p class="font-bold">000.000.000-00</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Email</p>
                            <p class="font-bold">joao.silva@example.com</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 border-r-2 w-1/3 pr-4">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">Endereço</h2>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Rua</p>
                            <p class="font-bold">Av. Brasil, 1234</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Cidade</p>
                            <p class="font-bold">São Paulo</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Estado</p>
                            <p class="font-bold">SP</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">CEP</p>
                            <p class="font-bold">01234-567</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-400 font-bold">Observações</p>
                            <p class="font-bold">Cliente VIP</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 w-1/3">
                        <h2 class="mb-5 text-lg font-semibold border-b-1 border-black dark:border-white">Veículos Cadastrados</h2>
                        <div class="flex flex-col gap-1 rounded-3xl bg-gray-300 p-5">
                            <div class="flex flex-row gap-2">
                                <Car class="h-6 w-6" />
                                <p class="">Toyota Corolla</p>
                            </div>
                            <div class="flex flex-row gap-1">
                                <p class="font-bold">Placa: </p>
                                <p class="font-bold">ABS-1234</p>
                            </div>
                            <div class="flex flex-row gap-1">
                                <p class="font-bold">Ano: </p>
                                <p class="font-bold">2018</p>
                            </div>
                        </div>
                    </div>
                    <div></div>
                </div>
            </DrawerContent>
        </Drawer>
    </div>
</template>