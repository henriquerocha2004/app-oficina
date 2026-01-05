<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
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
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';
import { 
  UserPlus, 
  Search, 
  MoreHorizontal, 
  UserCog, 
  Trash2, 
  Crown,
  Mail,
} from 'lucide-vue-next';
import type { User } from '@/types/user-management';

interface Props {
  users: User[];
  planLimits: {
    max_users: number | null;
    current_users: number;
  };
}

const props = defineProps<Props>();
const page = usePage();

const searchQuery = ref('');

const filteredUsers = computed(() => {
  if (!searchQuery.value) return props.users;
  
  const query = searchQuery.value.toLowerCase();
  return props.users.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query) ||
    user.role?.name.toLowerCase().includes(query)
  );
});

const canInviteUsers = computed(() => {
  if (!props.planLimits.max_users) return true;
  return props.planLimits.current_users < props.planLimits.max_users;
});

const inviteUser = () => {
  router.visit('/users/invite');
};

const changeRole = (userId: string) => {
  router.visit(`/users/${userId}/change-role`);
};

const deleteUser = (userId: string, userName: string) => {
  if (confirm(`Tem certeza que deseja excluir o usuário ${userName}?`)) {
    router.delete(`/users/${userId}`);
  }
};

const resendInvitation = (email: string) => {
  router.post('/invitations/resend', { email }, {
    preserveScroll: true,
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Gerenciar Usuários" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Usuários</h1>
          <p class="text-muted-foreground mt-1">
            Gerencie os usuários e suas permissões
            <span v-if="planLimits.max_users" class="ml-2">
              ({{ planLimits.current_users }}/{{ planLimits.max_users }} usuários)
            </span>
          </p>
        </div>
        <Button
          @click="inviteUser"
          :disabled="!canInviteUsers"
          class="gap-2"
        >
          <UserPlus class="h-4 w-4" />
          Convidar Usuário
        </Button>
      </div>

      <!-- Plan limit warning -->
      <div
        v-if="!canInviteUsers"
        class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-md"
      >
        Você atingiu o limite de usuários do seu plano. Faça upgrade para adicionar mais usuários.
      </div>

      <!-- Search -->
      <div class="flex items-center gap-4">
        <div class="relative flex-1 max-w-sm">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            v-model="searchQuery"
            placeholder="Buscar por nome, email ou role..."
            class="pl-9"
          />
        </div>
      </div>

      <!-- Users table -->
      <div class="border rounded-lg">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nome</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Role</TableHead>
              <TableHead>Status</TableHead>
              <TableHead class="w-[70px]"></TableHead>
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
                <Badge variant="outline" class="text-green-600 border-green-200">
                  Ativo
                </Badge>
              </TableCell>
              <TableCell>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button
                      variant="ghost"
                      size="icon"
                      :disabled="user.is_owner"
                    >
                      <MoreHorizontal class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="changeRole(user.id)">
                      <UserCog class="mr-2 h-4 w-4" />
                      Alterar Role
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      @click="deleteUser(user.id, user.name)"
                      class="text-red-600"
                    >
                      <Trash2 class="mr-2 h-4 w-4" />
                      Excluir
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="filteredUsers.length === 0">
              <TableCell colspan="5" class="text-center text-muted-foreground py-8">
                Nenhum usuário encontrado
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Invitations link -->
      <div class="flex justify-end">
        <Button
          variant="link"
          @click="router.visit('/invitations')"
          class="gap-2"
        >
          <Mail class="h-4 w-4" />
          Ver convites pendentes
        </Button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
