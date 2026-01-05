<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Search, UserCog, Crown } from 'lucide-vue-next';
import type { User } from '@/types/user-management';

interface Props {
  tenant: {
    id: string;
    name: string;
  };
  users: User[];
  open: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  close: [];
}>();

const searchQuery = ref('');

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users;
  
  const query = searchQuery.value.toLowerCase();
  return props.users.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  );
});

const impersonate = (userId: string) => {
  router.post(`/admin/tenants/${props.tenant.id}/users/${userId}/impersonate`, {}, {
    onSuccess: () => {
      // User will be redirected to tenant dashboard
    },
  });
};

const handleClose = () => {
  emit('close');
};
</script>

<template>
  <Dialog :open="open" @update:open="handleClose">
    <DialogContent class="max-w-3xl max-h-[80vh] overflow-hidden flex flex-col">
      <DialogHeader>
        <DialogTitle>Selecionar Usuário para Impersonate</DialogTitle>
        <DialogDescription>
          Tenant: <strong>{{ tenant.name }}</strong>
        </DialogDescription>
      </DialogHeader>

      <div class="flex-1 overflow-y-auto space-y-4">
        <!-- Search -->
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            v-model="searchQuery"
            placeholder="Buscar por nome ou email..."
            class="pl-9"
          />
        </div>

        <!-- Users table -->
        <div class="border rounded-lg">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Nome</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Role</TableHead>
                <TableHead class="w-[100px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="user in filteredUsers"
                :key="user.id"
              >
                <TableCell class="font-medium">
                  <div class="flex items-center gap-2">
                    {{ user.name }}
                    <Crown
                      v-if="user.is_owner"
                      class="h-4 w-4 text-yellow-500"
                      title="Proprietário"
                    />
                  </div>
                </TableCell>
                <TableCell class="text-muted-foreground">
                  {{ user.email }}
                </TableCell>
                <TableCell>
                  <Badge variant="secondary">
                    {{ user.role?.name }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <Button
                    size="sm"
                    @click="impersonate(user.id)"
                    class="gap-2"
                  >
                    <UserCog class="h-4 w-4" />
                    Impersonate
                  </Button>
                </TableCell>
              </TableRow>
              <TableRow v-if="filteredUsers.length === 0">
                <TableCell colspan="4" class="text-center text-muted-foreground py-8">
                  Nenhum usuário encontrado
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>

      <div class="flex justify-end pt-4">
        <Button variant="outline" @click="handleClose">
          Cancelar
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>
