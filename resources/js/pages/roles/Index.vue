<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
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
  Shield, 
  Search, 
  MoreHorizontal, 
  Settings, 
  Trash2,
  Plus,
  Lock,
} from 'lucide-vue-next';
import type { Role } from '@/types/user-management';

interface Props {
  roles: Role[];
}

const props = defineProps<Props>();

const searchQuery = ref('');

const filteredRoles = computed(() => {
  if (!searchQuery.value) return props.roles;
  
  const query = searchQuery.value.toLowerCase();
  return props.roles.filter(role => 
    role.name.toLowerCase().includes(query) ||
    role.description?.toLowerCase().includes(query)
  );
});

const createRole = () => {
  router.visit('/roles/create');
};

const managePermissions = (roleId: string) => {
  router.visit(`/roles/${roleId}/permissions`);
};

const editRole = (roleId: string) => {
  router.visit(`/roles/${roleId}/edit`);
};

const deleteRole = (roleId: string, roleName: string) => {
  if (confirm(`Tem certeza que deseja excluir a role "${roleName}"? Esta ação não pode ser desfeita.`)) {
    router.delete(`/roles/${roleId}`);
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Gerenciar Roles" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Roles e Permissões</h1>
          <p class="text-muted-foreground mt-1">
            Gerencie as funções e suas permissões no sistema
          </p>
        </div>
        <Button @click="createRole" class="gap-2">
          <Plus class="h-4 w-4" />
          Nova Role
        </Button>
      </div>

      <!-- Search -->
      <div class="flex items-center gap-4">
        <div class="relative flex-1 max-w-sm">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            v-model="searchQuery"
            placeholder="Buscar roles..."
            class="pl-9"
          />
        </div>
      </div>

      <!-- Roles table -->
      <div class="border rounded-lg">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nome</TableHead>
              <TableHead>Descrição</TableHead>
              <TableHead>Tipo</TableHead>
              <TableHead>Permissões</TableHead>
              <TableHead class="w-[70px]"></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="role in filteredRoles"
              :key="role.id"
            >
              <TableCell class="font-medium">
                <div class="flex items-center gap-2">
                  <Shield class="h-4 w-4 text-muted-foreground" />
                  {{ role.name }}
                </div>
              </TableCell>
              <TableCell class="text-muted-foreground">
                {{ role.description || '-' }}
              </TableCell>
              <TableCell>
                <Badge
                  v-if="role.is_system"
                  variant="outline"
                  class="text-blue-600 border-blue-200"
                >
                  <Lock class="mr-1 h-3 w-3" />
                  Sistema
                </Badge>
                <Badge v-else variant="secondary">
                  Customizada
                </Badge>
              </TableCell>
              <TableCell>
                <Button
                  variant="link"
                  size="sm"
                  @click="managePermissions(role.id)"
                  class="h-auto p-0"
                >
                  {{ role.permissions?.length || 0 }} permissões
                </Button>
              </TableCell>
              <TableCell>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon">
                      <MoreHorizontal class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="managePermissions(role.id)">
                      <Settings class="mr-2 h-4 w-4" />
                      Gerenciar Permissões
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      v-if="!role.is_system"
                      @click="editRole(role.id)"
                    >
                      <Settings class="mr-2 h-4 w-4" />
                      Editar
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      v-if="!role.is_system"
                      @click="deleteRole(role.id, role.name)"
                      class="text-red-600"
                    >
                      <Trash2 class="mr-2 h-4 w-4" />
                      Excluir
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="filteredRoles.length === 0">
              <TableCell colspan="5" class="text-center text-muted-foreground py-8">
                Nenhuma role encontrada
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- System roles info -->
      <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md text-sm">
        <strong>Roles do Sistema:</strong> Owner, Manager, Attendant, Mechanic e Viewer são roles predefinidas do sistema e não podem ser excluídas. Você pode customizar suas permissões conforme necessário.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
